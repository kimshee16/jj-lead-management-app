<?php

namespace App\Http\Controllers;

use App\Models\LeadClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeadClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leadClients = LeadClient::latest()->paginate(15);
        return view('lead_clients.index', compact('leadClients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lead_clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'mobile_number' => [
                'nullable',
                'string',
                'max:191',
                'regex:/^\+?[0-9]{10,15}$/', // Accepts + and 10-15 digits
            ],
            'note' => 'nullable|string',
            'status' => 'required|in:new_lead,spam,junk,clear,unmarked',
            'lead_type' => 'required|in:manual,webhook,ppc',
            'user_status' => 'required|in:normal,agent',
            'user_type' => 'required|in:admin,user',
            'admin_status' => 'required|in:Contacted,Appointment set,Burst,call_back_later,interested,not_interested,wrong_number,not_reachable,dnd,not_eligible',
            'is_admin_spam' => 'required|in:0,1',
        ]);
        
        $data = $request->all();
        $data['client_id'] = session('user.id');
        $data['added_by_id'] = session('user.id');
        $leadClient = LeadClient::create($data);

        // --- Make API call right after creating the lead ---
        $apiKey = env('VAPI_API_KEY');
        $assistantId = env('VAPI_ASSISTANT_ID');
        $phoneNumberId = env('VAPI_PHONE_ID');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.vapi.ai/call', [
            'assistantId' => $assistantId,
            'phoneNumberId' => $phoneNumberId,
            'customer' => [
                'number' => $leadClient->mobile_number,
            ],
        ]);

        $redirect = redirect()->route('lead-clients.index');

        if ($response->successful()) {
            return $redirect->with('success', 'Lead client created and API call initiated for ' . $leadClient->name);
        }
        
        return $redirect->with([
            'success' => 'Lead client created successfully for ' . $leadClient->name,
            'error' => 'But failed to initiate API call: ' . $response->body(),
        ]);
    }
}
