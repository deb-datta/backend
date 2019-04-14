<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase;
use File;
use Image;
use App\SessionData;

class ImageController extends Controller
{
    public function sessionUpload(Request $request, Firebase $firebase, $unique_id = '')
    {
    	$file = $request->file('file');
    	$width = 500;
		$height = (500*.75);
    	if($request->hasFile('file')){
    		// foreach ($files as $file) {
    			$filename = $file->getClientOriginalName();
    			
		        
		        // update newsfeed firebase
		        $session = SessionData::where('unique_id', $unique_id)->first();
		        $databasefbs = $firebase->getDatabase();
		        $newPostKey2 = $databasefbs->getReference('mars2/newsfeed/'.$session->session_id)->push()->getKey();
		        $updates2 = [
		            'mars2/newsfeed/'.$session->session_id.'/image_status' => '1',
		        ];
		        $donenoww2= $databasefbs->getReference()->update($updates2);

		        return response()->json([
		            'response' => 'success',
		            'message' => "Data Saved"
		        ], 200);
        	// }
        }
    }
}