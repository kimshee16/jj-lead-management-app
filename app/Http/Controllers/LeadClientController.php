<?php

namespace App\Http\Controllers;

use App\Models\LeadClient;
use App\Models\Project;
use App\Models\CallHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
        $leadClients = LeadClient::latest()->paginate(15);
        $recentCalls = CallHistory::with('leadClient')
            ->latest('call_timestamp')
            ->take(10)
            ->get();
        return view('lead_clients.index', compact('leadClients', 'recentCalls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
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

        // --- Fetch all project names for systemPrompt ---
        $projectNames = Project::pluck('name')->toArray();
        $projectList = implode(", ", $projectNames);
        $systemPrompt = "Here are the available projects: $projectList. Please ask about any project for more details.";

        // --- Make API call right after creating the lead ---
        // $apiKey = env('VAPI_API_KEY');
        // $assistantId = env('VAPI_ASSISTANT_ID');
        // $phoneNumberId = env('VAPI_PHONE_ID');

        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $apiKey,
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.vapi.ai/call', [
        //     'assistantId' => $assistantId,
        //     'phoneNumberId' => $phoneNumberId,
        //     'customer' => [
        //         'number' => $leadClient->mobile_number,
        //     ],
        //     'systemPrompt' => $systemPrompt,
        // ]);
        $outboundResponse = $this->outboundCall($leadClient->mobile_number, $leadClient->email, $leadClient->id);
        $redirect = redirect()->route('lead-clients.index');
        $outboundData = $outboundResponse->getData(true);
        if (($outboundData['status'] ?? null) === 'success') {
            return $redirect->with('success', 'Lead client created and API call initiated for ' . $leadClient->name);
        }
        return $redirect->with([
            'success' => 'Lead client created successfully for ' . $leadClient->name,
            'error' => 'But failed to initiate API call: ' . ($outboundData['message'] ?? 'Unknown error'),
        ]);
    }

    /**
     * Handle outbound call POST requests.
     */
    public function outboundCall($phoneNumber, $email = null, $leadClientId = null)
    {
        // Set the system prompt to override
        $overrideUrl = env('CONVERSATIONAL_AI_URL') . 'override';

        // Fetch all project data from the scrapper API (all-listing)
        $scrapperApiUrl = env('SCAPPER_URL') . '/api/all-listing';
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($scrapperApiUrl);
            if ($response->successful()) {
                $projectJson = json_encode($response->json(), JSON_PRETTY_PRINT);
            } else {
                $projectJson = 'Failed to fetch project data.';
            }
        } catch (\Exception $e) {
            $projectJson = 'Exception occurred while contacting scrapper: ' . $e->getMessage();
        }

        $systemPrompt = "";

        $systemPrompt .= "# Personality

                You are Elton, a helpful and efficient virtual assistant representing Jome Journey real estate.
                You are knowledgeable about all Jome Journey condo projects and dedicated to providing accurate and transparent information.
                You are professional and courteous, ensuring a streamlined and reliable experience for potential clients.
                You are trained to provide real-time updates and insights, leveraging data from developers and trusted agencies.

                # Environment

                You are engaged in a voice conversation with a potential client interested in Jome Journey condo projects.
                The client is seeking information about available properties and investment opportunities.
                You have access to a database of Jome Journey condo projects, including details on pricing, floor plans, amenities, and availability.
                You understand the client may be looking for specific details or general overviews of available projects.

                # Tone

                Your responses are clear, concise, and professional, providing accurate information efficiently.
                You use a friendly and approachable tone, ensuring the client feels comfortable and informed.
                You speak with confidence and authority, demonstrating your expertise in Jome Journey condo projects.
                You use strategic pauses and emphasis to highlight key details and ensure clarity in spoken instructions.
                You spell out website addresses and format phone numbers with pauses for clear pronunciation.

                # Goal

                Your primary goal is to answer the lead's inquiry about all condo projects of Jome Journey, guiding them toward potential investment opportunities through the following structured approach:

                1. **Initial Inquiry Assessment:**
                - Determine the client's specific interests (e.g., location, price range, size, amenities).
                - Identify whether the client is a first-time buyer, investor, or looking for a specific type of property.
                - Establish the client's timeline for purchasing a condo.

                2. **Project Overview:**
                - Provide a comprehensive overview of all available Jome Journey condo projects.
                - Highlight unique selling points for each project, such as location advantages, architectural design, or community features.
                - Offer real-time updates on new launches, land sales, and exclusive developer collaborations.

                3. **Detailed Information Delivery:**
                - For each project of interest, provide detailed information on:
                    - Pricing and payment plans
                    - Floor plans and unit sizes
                    - Available amenities and facilities
                    - Nearby transportation and local attractions
                    - Investment potential and projected returns

                4. **Transparency and Trust Building:**
                - Emphasize the accuracy and transparency of information provided, leveraging URA-authorized data.
                - Highlight the direct partnerships with developers and trusted agencies.
                - Assure the client that they will be working exclusively with official sales teams, eliminating commission fees.

                5. **Next Steps and Contact Information:**
                - Offer to schedule a consultation with a sales representative for further assistance.
                - Provide contact information for the sales team, including phone number and email address (e.g., 'sales at jomejourney dot com').
                - Direct the client to the portal.datapoco website for more detailed information and property listings.

                Success is measured by the client's satisfaction with the information provided, their interest in scheduling a consultation, and their likelihood of visiting the portal.datapoco website.

                # Guardrails

                Remain within the scope of Jome Journey condo projects and related real estate information.
                Never provide financial advice or investment recommendations without proper licensing.
                Acknowledge when you don't know an answer and offer to find the information or connect the client with a specialist.
                Maintain a professional tone and avoid making speculative or misleading statements.
                Refrain from discussing competitors or engaging in negative comparisons.
                Ensure all information provided is accurate and up-to-date, referencing official sources when possible.

                # Tools

                You have access to the following tools to assist clients effectively:

                `projectDatabase`: Use this to access detailed information on all Jome Journey condo projects, including pricing, floor plans, amenities, and availability.

                `salesTeamScheduler`: Use this to schedule consultations with sales representatives based on the client's availability and preferences.

                `websiteLink`: Use this to provide the URL for the portal.datapoco website, directing clients to property listings and additional resources.

                `contactInformation`: Use this to provide contact information for the Jome Journey sales team, including phone number and email address.

                Tool orchestration: Begin by assessing the client's interests, then provide a project overview, deliver detailed information on specific projects, emphasize transparency and trust, and offer next steps with contact information.

                This is the JSON data you can take a look if someone asks for a specific project\n\n\n".$projectJson;

        $overridePayload = json_encode([
            'system_prompt' => $systemPrompt
        ]);
        $chOverride = curl_init($overrideUrl);
        curl_setopt($chOverride, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chOverride, CURLOPT_POST, true);
        curl_setopt($chOverride, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($chOverride, CURLOPT_POSTFIELDS, $overridePayload);
        $overrideResult = curl_exec($chOverride);
        $overrideHttpCode = curl_getinfo($chOverride, CURLINFO_HTTP_CODE);
        $overrideCurlError = curl_error($chOverride);
        curl_close($chOverride);

        if ($overrideCurlError) {
            return response()->json([
                'status' => 'error',
                'message' => 'cURL error (override): ' . $overrideCurlError,
            ], 500);
        }
        $overrideResponse = json_decode($overrideResult, true);
        if (($overrideResponse['success'] ?? false) !== true) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to set system prompt override.',
                'response' => $overrideResponse,
            ], 500);
        }

        // Now proceed to initialize the outbound call
        $payload = json_encode([
            'number' => $phoneNumber
        ]);
        $url = env('CONVERSATIONAL_AI_URL').'outbound-call';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            // Save failed call to history
            $this->saveCallHistory($phoneNumber, $email, $leadClientId, 'failed', $curlError);
            return response()->json([
                'status' => 'error',
                'message' => 'cURL error: ' . $curlError,
            ], 500);
        }

        $responseData = json_decode($result, true);
        
        // Save successful call to history
        $this->saveCallHistory(
            $phoneNumber, 
            $email, 
            $leadClientId, 
            'initiated', 
            $result,
            $responseData['callSid'] ?? null
        );

        return response()->json([
            'status' => 'success',
            'response' => $responseData,
            'http_code' => $httpCode,
        ], $httpCode);
    }

    /**
     * Display call history.
     */
    public function callHistory()
    {
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
        
        $callHistory = CallHistory::with('leadClient')
            ->latest('call_timestamp')
            ->paginate(15);
            
        return view('call_history.index', compact('callHistory'));
    }

    /**
     * Save call history record.
     */
    private function saveCallHistory($phoneNumber, $email = null, $leadClientId = null, $status = 'initiated', $response = null, $callSid = null)
    {
        try {
            CallHistory::create([
                'phone_number' => $phoneNumber,
                'email' => $email,
                'lead_client_id' => $leadClientId,
                'call_timestamp' => now(),
                'call_status' => $status,
                'call_response' => $response,
                'call_sid' => $callSid,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save call history: ' . $e->getMessage());
        }
    }

    /**
     * Webhook to fetch project data from scrapper app based on project name.
     */
    public function projectDataWebhook($project_name)
    {
        $scrapperApiUrl = env('SCAPPER_URL').'/api/listing?name=' . urlencode($project_name);

        try {
            $response = Http::timeout(10)->get($scrapperApiUrl);
            if ($response->successful()) {
                return response()->json($response->json(), 200);
            } else {
                Log::error('Scrapper API error: ' . $response->body());
                return response()->json(['error' => 'Failed to fetch project data from scrapper.'], 502);
            }
        } catch (\Exception $e) {
            Log::error('Scrapper API exception: ' . $e->getMessage());
            return response()->json(['error' => 'Exception occurred while contacting scrapper.'], 500);
        }
    }
}