<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MessageController extends Controller
{
    public function message(Request $request){
        $message = Message::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'message'=>$request->message,
         
        ]);

        Session::flash("messages", 'Message sent successfully');
        return redirect()->back();

        }

        public function adminMessages(){

            $messages = Message::all();

            return view("Admin.Message.messages", [
                "messages" => $messages,
            ]);
        }

        public function deleteMessage($id)
        {
            Message::find($id)->delete();

            Session::flash("message", "Message Deleted");
            return redirect()->back();
        }

        public function replyMessage($id)
        {
            $message = Message::find($id);
            $message->status = "replied";
            $message->update();

            Session::flash("message", "Message Status Updated");
            return redirect()->back();
        }

        public function pendingMessage($id)
        {
            $message = Message::find($id);
            $message->status = "pending";
            $message->update();

            Session::flash("message", "Message Status Updated");
            return redirect()->back();
        }
}
