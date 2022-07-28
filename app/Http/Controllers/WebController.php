<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebRequest;
use App\Http\Requests\UpdateWebRequest;
use App\Models\Web;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = Web::select('title')->distinct()->get();
        return view('web.index', ['data' => $data]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('web.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWebRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebRequest $request)
    {
        $data = $request->validated();
        $data['owner'] = session('user')->account;
        if (!empty($data['url'])) {
            $url = $data['url'];

            if (parse_url($url, PHP_URL_SCHEME) !== 'http' && parse_url($url, PHP_URL_SCHEME) !== 'https') {
                $url = 'http://' . $url;
            }
            $number = Web::where('url', $url)->get();
            if ($number->count() === 0) {
                $data['url'] = $url;
                $ch = curl_init($data['url']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $data['http_code'] = $http_code;
                if (!curl_errno($ch)) {
                    $data['status'] = 'alive';
                } else {
                    $data['status'] = 'die';
                }
                $data['last_check'] = date('Y-m-d H:i:s');
                if (Web::create($data)) {
                    $text = 'User ' . '<b>' . session('user')->account . '</b>' . ' created new url ' . $data['url'] . ' to ' . $data['title'] . ' at ' . date('Y-m-d H:i:s');
                    Telegram::sendMessage([
                        'chat_id' => env('TELEGRAM_CHANNEL_ID', '-796261100'),
                        'parse_mode' => 'HTML',
                        'text' => $text
                    ]);
                }
            }
        }
        if ($request['file']) {
            $urls = fopen($request['file'], 'r');
            while (!feof($urls)) {
                $url = fgets($urls);
                $url = trim($url);
                if (!empty($url)) {
                    if (parse_url($url, PHP_URL_SCHEME) !== 'http' && parse_url($url, PHP_URL_SCHEME) !== 'https') {
                        $url = 'http://' . $url;
                    }
                    $data['url'] = $url;
                    $number = Web::where('url', $url)->get();
                    if ($number->count() > 0) {
                        continue;
                    }
                    $data['last_check'] = date('Y-m-d H:i:s');
                    $ch = curl_init($data['url']);

                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $data['http_code'] = $http_code;
                    if (!curl_errno($ch)) {
                        $data['status'] = 'alive';
                    } else {
                        $data['status'] = 'die';
                    }
                    if (Web::create($data)) {
                        $text = 'User ' . '<b>' . session('user')->account . '</b>' . ' created new url ' . $data['url'] . ' to ' . $data['title'] . ' at ' . date('Y-m-d H:i:s');
                        Telegram::sendMessage([
                            'chat_id' => env('TELEGRAM_CHANNEL_ID', '-796261100'),
                            'parse_mode' => 'HTML',
                            'text' => $text
                        ]);
                    }
                }
            }
            fclose($urls);
        }
        return view('web.create');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Web  $web
     * @return \Illuminate\Http\Response
     */
    public function show($web)
    {
        $data = Web::where('title', $web)->get();
        return view('web.show', ['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Web  $web
     * @return \Illuminate\Http\Response
     */
    public function edit(Web $web)
    {
        //
        return view('web.edit', ['web' => $web]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWebRequest  $request
     * @param  \App\Models\Web  $web
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWebRequest $request, Web $web)
    {
        $data = $request->validated();
        if (!empty($data['url'])) {
            $url = $data['url'];
            if (parse_url($url, PHP_URL_SCHEME) !== 'http' && parse_url($url, PHP_URL_SCHEME) !== 'https') {
                $url = 'http://' . $url;
            }
            $data['url'] = $url;
            if ($data['url'] !== $web->url && Web::where('url', $data['url'])->get()->count() === 0) {
                $ch = curl_init($data['url']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $data['http_code'] = $http_code;
                if (!curl_errno($ch)) {
                    $data['status'] = 'alive';
                } else {
                    $data['status'] = 'die';
                }
                $web->update($data);
                return redirect()->route('web.show', ['web' => $web->title]);
            }
            return redirect()->route('web.edit', ['web' => $web]);
        }
    }
    public function recheck(Web $web)
    {
        $url = $web->url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $data['http_code'] = $http_code;
        if (!curl_errno($ch)) {
            $data['status'] = 'alive';
        } else {
            $data['status'] = 'die';
        }
        $data['last_check'] = date('Y-m-d H:i:s');
        if ($data['status'] !== $web->status) {
            if ($data['status'] === 'die') {
                $text = '<b>' . date('H:i') . '</b>'
                    . ' domain: ' . $url . ' died.';
                Telegram::sendMessage([
                    'chat_id' => env('TELEGRAM_CHANNEL_ID', '-796261100'),
                    'parse_mode' => 'HTML',
                    'text' => $text
                ]);
            }
        }
        $web->update($data);
        return redirect()->route('web.show', ['web' => $web->title]);
    }
    public function addurl($title)
    {
        return view('web.addurl', ['title' => $title]);
    }
    public function storeurl(Request $request, $title)
    {

        $data['url'] = $request['url'];
        $data['title'] = $title;
        $data['owner'] = session('user')->account;
        if (!empty($data['url'])) {
            $url = $data['url'];
            if (parse_url($url, PHP_URL_SCHEME) !== 'http' && parse_url($url, PHP_URL_SCHEME) !== 'https') {
                $url = 'http://' . $url;
            }
            $data['url'] = $url;
            $number = Web::where('url', $url)->get();
            if ($number->count() > 0) {
                return redirect()->route('web.show', ['web' => $title]);
            }
            $ch = curl_init($data['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $data['http_code'] = $http_code;
            if (!curl_errno($ch)) {
                $data['status'] = 'alive';
            } else {
                $data['status'] = 'die';
            }
            $data['last_check'] = date('Y-m-d H:i:s');
            if (Web::create($data)) {
                $text = 'User ' . '<b>' . session('user')->account . '</b>' . ' created new url ' . $data['url'] . ' to ' . $data['title'] . ' at ' . date('Y-m-d H:i:s');
                Telegram::sendMessage([
                    'chat_id' => env('TELEGRAM_CHANNEL_ID', '-796261100'),
                    'parse_mode' => 'HTML',
                    'text' => $text
                ]);
            }
        }
        $data = Web::where('title', $title)->get();
        return view('web.show', ['data' => $data]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Web  $web
     * @return \Illuminate\Http\Response
     */
    public function destroy(Web $web)
    {
        //
        $web->delete();
        return redirect()->route('web.index');
    }
    public function destroy_title($web)
    {
        //
        Web::where('title', $web)->delete();
        return redirect()->route('web.index');
    }
    public function recheckTimes()
    {
        $webs = Web::get();
        foreach ($webs as $web) {
            $this->recheck($web);
        }
    }
}