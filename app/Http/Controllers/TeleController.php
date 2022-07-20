<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Requests\StoreWebRequest;
use App\Http\Requests\UpdateWebRequest;
use App\Models\Web;


class TeleController extends Controller
{
    public function updatedActivity($url)
    {   
        $text1 ='<b>'.date('H:i').'</b>\n'
        .' domain: '.$url.' died.';
        $activity = Telegram::sendMessage([
            'chat_id' => '-796261100',
            'parse_mode' => 'HTML',
            'text' => $text1,
        ]);
    }
}