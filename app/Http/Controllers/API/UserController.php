<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\User;

class UserController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
      // $this->authorize('isAdmin');
      if (\Gate::allows('isAdmin')) 
      {
        return User::latest()->paginate(5);
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'  => 'required|string|max:191',
            'email'  => 'required|string|email|max:191|unique:users',
            'password'  => 'required|string|min:6',
        ]);
        return User::create([
            'name'     => $request['name'],
            'email'    => $request['email'],
            'type'     => $request['type'],
            'bio'      => $request['bio'],
            'photo'    => $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }


    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $this->validate($request,[
            'name'  => 'required|string|max:191',
            'email'  => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'  => 'sometimes|required|min:6',
        ]);

        $currentPhoto = $user->photo;

        // Check if name of previous photo is not equal to current photo then upload;
        if ($request->photo != $currentPhoto) {
            
          // making unique name of image so it can't be repeated;
            $name = time().'.'.explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];

          // Now using image intervention to save our image;
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

          // storing new name value using merge function;
            $request->merge(['photo'=>$name]);

          // Deleting current photo while uploading new one;
          //Step-1:- get current photo directory path:
            $userPhoto = public_path('img/profile/').$currentPhoto;
          //Step-2:- Check if already file exits and then delete photo using unlink;
            if (file_exists($userPhoto)) {
                @unlink($userPhoto);
            }
        }

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
        }


        $user->update($request->all());

        return ['message'=>"success"];
    }


    public function profile()
    {
        return auth('api')->user();
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $this->validate($request,[
            'name'  => 'required|string|max:191',
            'email'  => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'  => 'sometimes|min:6',
        ]);

        $user->update($request->all());
        return [ 'message' => 'Update the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);

        $user->delete();

        return ['message' => 'User Deleted'];
    }

    public function search()
    {
      if ($search = \Request::get('q')) {
        $users = User::where( function ($query) use ($search) {
          $query->where('name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->orWhere('type','LIKE',"%$search%");
        })->paginate(5);
      }
      else {
        $users = User::latest()->paginate(5);
      }

      return $users;
    }
}
