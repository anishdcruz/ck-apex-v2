<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

// global route middleware to authorize

class UserController extends Controller
{
    public function index()
    {
        return api([
            'data' => User::search()
        ]);
    }

    public function create()
    {
        $form = [
            'name' => '',
            'title' => null,
            'telephone' => null,
            'extension' => null,
            'mobile_number' => null,
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'email_signature' => 'Best Regards',
            'is_admin' => 0,
            'is_active' => 1,
            'company' => settings()->get('company_name')
        ];

        return api([
            'form' => $form
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'title' => 'nullable|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'mobile_number' => 'nullable|max:255',
            'telephone' => 'nullable|max:255',
            'extension' => 'nullable|max:255',
            'password' => 'required|confirmed|min:6|max:60',
            'email_signature' => 'required|max:255',
            'is_admin' => 'required|boolean',
            'is_active' => 'required|boolean'
        ]);

        $model = new User;
        $model->fill($request->all());
        $model->is_admin = $request->is_admin;
        $model->is_active = $request->is_active;
        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function show($id)
    {
        return api([
            'data' => User::findOrFail($id)
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $user->company = settings()->get('company_name');
        return api([
            'form' => $user
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = User::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'title' => 'nullable|max:255',
            'email' => 'required|email|unique:users,email,'.$model->id.',id',
            'mobile_number' => 'nullable|max:255',
            'telephone' => 'nullable|max:255',
            'extension' => 'nullable|max:255',
            'password' => 'sometimes|confirmed|min:6|max:60',
            'email_signature' => 'required|max:255',
            'is_admin' => 'required|boolean',
            'is_active' => 'required|boolean'
        ]);

        $model->fill($request->all());
        $model->is_admin = $request->is_admin;
        $model->is_active = $request->is_active;
        $model->save();

        return api([
            'saved' => true,
            'id' => $model->id
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('delete');

        $model = User::findOrFail($id);

        // cannot self delete

        if(auth()->id() == $model->id) {
            return api([
                'errors' => [],
                'message' => 'Cannot delete yourself!'
            ], 422);
        }

        // delete user
        $model->delete();

        return api([
            'deleted' => true
        ]);
    }
}
