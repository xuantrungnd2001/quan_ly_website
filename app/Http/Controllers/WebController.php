<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWebRequest;
use App\Http\Requests\UpdateWebRequest;
use App\Models\Web;
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
        // dd($data);
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
        //
        $data = $request->validated();
        $data['owner'] = session('user')->account;
        if (!empty($data['url'])) {
            $url = $data['url'];
            if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
                $url = 'http://' . $url;
            }
            $data['url'] = $url;
            $ch = curl_init($data['url']);
            curl_exec($ch);
            if (!curl_errno($ch)) {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($http_code >= 400) {
                    $data['status'] = 'die';
                } else {
                    $data['status'] = 'alive';
                }
            } else {
                $data['status'] = 'die';
            }
            Web::create($data);
        }

        if ($request['file']) {
            $urls = fopen($request['file'], 'r');
            while (!feof($urls)) {
                $url = fgets($urls);
                $url = trim($url);
                if (!empty($url)) {
                    if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $url !== "\n") {
                        $url = 'http://' . $url;
                    }
                    $data['url'] = $url;
                    $ch = curl_init($data['url']);
                    curl_exec($ch);
                    if (!curl_errno($ch)) {
                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if ($http_code >= 400) {
                            $data['status'] = 'die';
                        } else {
                            $data['status'] = 'alive';
                        }
                    } else {
                        $data['status'] = 'die';
                    }
                    Web::create($data);
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
    // public function status($web)
    // {

    //     $data = Web::where('title', $web)->get();

    //     return view('web.show', ['data' => $data]);
    // }
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
        //
        $data = $request->validated();
        if (!empty($data['url'])) {
            $url = $data['url'];
            if (strpos($url, 'http://') === false && strpos($url, 'https://') === false) {
                $url = 'http://' . $url;
            }
            $data['url'] = $url;
            if ($data['url'] !== $web->url) {
                $ch = curl_init($data['url']);
                curl_exec($ch);
                if (!curl_errno($ch)) {
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($http_code >= 400) {
                        $data['status'] = 'die';
                    } else {
                        $data['status'] = 'alive';
                    }
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
        curl_exec($ch);
        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code >= 400) {
                $data['status'] = 'die';
            } else {
                $data['status'] = 'alive';
            }
        } else {
            $data['status'] = 'die';
        }
        if ($data['status'] !== $web->status) {
            $web->update($data);
        }
        $text = "Test huhu\n";
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '1714366965'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
        return redirect()->route('web.show', ['web' => $web->title]);
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