<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormEmail;
use Illuminate\Support\Facades\Log;

class ContactsController extends Controller
{
    public function sendContactEmail(Request $request)
    {
        try {
            if ($request->getMethod() == 'POST') {
                $contactEmailParams = $this->handleContactEmailParams($request);
                if (!empty($contactEmailParams['name']) && !empty($contactEmailParams['content']) && !empty($contactEmailParams['email'])) {
                    $email = new ContactFormEmail($contactEmailParams['name'], $contactEmailParams['content'], $contactEmailParams['email']);
                    Mail::to('customer.service@test.com')->send($email);
                    session()->flash('message', 'Contact message was sent');
                    return redirect()->route('homepage');
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

     /**
     * @param Request $request
     * @return array
     */
     private function handleContactEmailParams(Request $request): array
     {
         $name = '';

         if (!empty($request->get('name'))) {
             $name .= $request->get('name');
         }

         if (!empty($request->get('surname'))) {
             $name .=  ' ' . $request->get('surname');
         }

         return [
             'name' => trim($name),
             'content' => trim($request->get('message')),
             'email' => trim($request->get('email'))
         ];
     }
}
