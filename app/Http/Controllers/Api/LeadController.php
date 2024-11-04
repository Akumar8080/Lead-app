<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    /**
     * Lead List
     *
     * @param Request $request object
     *
     * @return string
    */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $leads = Lead::select(
                'id',
                'name',
                'email',
                'mobile',
                'description',
                'source',
                'status'
            )
            ->where(function ($query) use ($request) {
                $query->where('name', 'like','%' . $request->filter . '%')
                ->orWhere('email', 'like','%' . $request->filter . '%');
            });
            return datatables()->of($leads)
            ->addColumn('action', function ($lead) {
                return '<button onclick="editLead(' . $lead->id . ')">Edit</button>
                        <button onclick="showPostUpdateModal(' . $lead->id . ')">Post Update</button>
                        <button onclick="viewUpdates(' . $lead->id . ')">View Updates</button>';
            })->make(true);
        }
        return view('leads.index');
    }

    /**
     * Lead Store
     *
     * @param Request $request object
     *
     * @return string
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:leads',
            'mobile' => 'required|string|max:15|unique:leads',
            'description' => 'nullable|string',
            'status' => 'required|in:new,accepted,completed,rejected,invalid',
        ]);
        $lead = Lead::create($request->all());
        return response()->json($lead, 201);
    }

    /**
     * Lead show
     *
     * @param Request $request object
     *
     * @return string
    */
    public function show(Lead $lead)
    {
        return response()->json($lead);
    }

    /**
     * Lead update
     *
     * @param Request $request object
     *
     * @return string
    */
    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'unique:leads,email,'.$lead->id,
            'mobile' => 'unique:leads,mobile,'.$lead->id,
            'description' => 'nullable|string',
            'status' => 'required|in:new,accepted,completed,rejected,invalid',
        ]);
        $lead->find($lead->id)->update($request->all());
        return response()->json($lead);
    }

    /**
     * Lead post update
     *
     * @param Request $request object
     *
     * @return string
    */
    public function postUpdate(Request $request,$id)
    {
        $request->validate([
            'lead_message' => 'required|string',
            'user' => 'required|string'
        ]);
        LeadUpdate::create([
            'lead_id' => $id,
            'lead_message' => $request->lead_message,
            'user' => $request->user,
        ]);
        return response()->json(['message' => 'Update posted successfully'], 201);
    }

    /**
     * get lead updates data
     *
     * @param Request $request object
     *
     * @return string
    */
    public function getUpdates($id)
    {
        $updates = LeadUpdate::select(
            'id',
            'lead_id',
            'lead_message',
            'user',
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as created_at")
            )
        ->where('lead_id', $id)->get();
        return datatables()->of($updates)->make(true);
    }

    /**
     * redirect on view lead page
     *
     * @param Request $request object
     *
     * @return string
    */
    public function showUpdates($id)
    {
        return view('leads.updates', ['leadId' => $id]);
    }
}
