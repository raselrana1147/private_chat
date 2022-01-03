<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        if(\Request::ajax()){
            $users=User::where('id','!=',auth()->user()->id)->latest()->get();
            return response()->json($users,200);
        }
        abort(404);

    }

    public  function user_message($id){
        if(!\Request::ajax()){
            abort(404);
        }
        $user=User::findOrFail($id);
       $messages=$this->get_user_message($id);


        return response()->json([
            "messages"=>$messages,
            'user'=>$user,
            'status'=>200
        ]);
    }


    public function send_message(Request $request)
    {

        if(!\Request::ajax()){
            abort(404);
        }
        $messages=Message::create([
            'message'=>$request->message,
            'from'=>auth()->user()->id,
            'to'=>$request->user_id,
            'type'=>0
        ]);

        $messages=Message::create([
            'message'=>$request->message,
            'from'=>auth()->user()->id,
            'to'=>$request->user_id,
            'type'=>1
        ]);


     broadcast(new MessageSend($messages));
        return response()->json([
            'message'=>"inserted",
            'status'=>200
        ]);

    }

    public function delete_message($id)
    {
        $message=Message::findOrFail($id);
        $message->delete();

        response()->json([
            'data'=>'deleted',
            'status'=>200
        ]);
    }

    public function delete_all_message($id){
        $messages=$this->get_user_message($id);

        foreach($messages as $message){
            Message::findOrfail($message->id)->delete();
        }

        response()->json([
            'data'=>'deleted',
            'status'=>200
        ]);
    }

    public function get_user_message($id){
        $messages=Message::where(function ($query) use ($id) {
            $query->where('from', '=', Auth::user()->id)
                  ->where('to', '=', $id)
                  ->where('type', '=',0);
        })->orWhere(function ($query) use ($id) {
            $query->where('from', '=', $id)
                  ->where('to', '=', Auth::user()->id)
                  ->where('type', '=',1);
        })->with('user')->get();

        return $messages;
    }
}
