<?php

namespace Modules\UserProfile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\UserProfile\Services\NotificationSetupService;

class NotificationSetupController extends Controller
{
protected $setup;

    public function __construct(NotificationSetupService $setup)
    {
        $this->setup = $setup;
    }

    public function createNotification(Request $request){
     return $this->setup->createNotification($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('userprofile::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('userprofile::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('userprofile::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
