<?php
namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\User;
use App\Models\EnquiryReminder;
use App\Models\Property;
use Illuminate\Http\Request;
use Validator;
use App\Helpers\Helper;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $reminder_date_from = $request->reminder_date_from;
        $reminder_date_to = $request->reminder_date_to;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $project_id = $request->project_id;
        $assign_to_id = $request->assign_to_id;
        $status_id = $request->status_id;
        $rating = $request->rating;
        $source_id = $request->source_id;
        $quick_filter = $request->quick_filter;
        $search_str = $request->q;

        $query = Enquiry::with(['client', 'reminders', 'project', 'status', 'assignedTo', 'source', 'properties']);

        if (!empty($search_str)) {
            $query->where(function ($q) use ($search_str) {
                $q->whereHas('client', function ($q) use ($search_str) {
                    $q->where('first_name', 'like', '%' . $search_str . '%')
                        ->orWhere('last_name', 'like', '%' . $search_str . '%')
                        ->orWhere('email', 'like', '%' . $search_str . '%');
                })
                    ->orWhereHas('project', function ($q) use ($search_str) {
                        $q->where('name', 'like', '%' . $search_str . '%');
                    })
                    ->orWhereHas('status', function ($q) use ($search_str) {
                        $q->where('name', 'like', '%' . $search_str . '%');
                    })
                    ->orWhereHas('source', function ($q) use ($search_str) {
                        $q->where('name', 'like', '%' . $search_str . '%');
                    })
                    ->orWhere('general_remarks', 'like', '%' . $search_str . '%');
            });
        }
        if (!empty($reminder_date_from)) {
            $query->whereDate('reminder_datetime', '>=', $reminder_date_from);
        }
        if (!empty($reminder_date_to)) {
            $query->whereDate('reminder_datetime', '<=', $reminder_date_to);
        }
        if (!empty($date_from)) {
            $query->whereDate('created_at', '>=', $date_from);
        }
        if (!empty($date_to)) {
            $query->whereDate('created_at', '<=', $date_to);
        }
        if (!empty($project_id)) {
            $query->where('project_id', $project_id);
        }
        if (!empty($assign_to_id)) {
            $query->where('assign_to_id', $assign_to_id);
        }
        if (!empty($status_id)) {
            $query->where('enquiry_status_id', $status_id);
        }
        if (!empty($rating)) {
            $query->where('rating', $rating);
        }
        if (!empty($source_id)) {
            $query->where('source_id', $source_id);
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'required|exists:projects,id',
            'enquiry_status_id' => 'required|exists:enquiry_statuses,id',
            'rating' => 'string|nullable',
            'general_remarks' => 'string|nullable',
            'assign_to_id' => 'exists:users,id|nullable',
            'source_id' => 'exists:enquiry_sources,id|nullable',
            'source_remarks' => 'string|nullable',
            'followup_notes' => 'string|nullable',
            'closure_reason' => 'string|nullable',
            'junk_status_name' => 'string|nullable',
            'junk_reason' => 'string|nullable',
            'reminder_datetime' => 'date|nullable',
            'reminder_remarks' => 'string|nullable',
            'property_ids' => 'array|nullable',
            'property_ids.*' => 'exists:properties,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        $enquiry = Enquiry::create($request->except(['property_ids', 'reminder_datetime', 'reminder_remarks']));

        if ($request->property_ids) {
            $enquiry->properties()->sync($request->property_ids);
        }

        if ($request->reminder_datetime) {
            $enquiry->reminders()->create(attributes: [
                // 'enquiry_id' => $enquiry->id,
                'reminder_datetime' => $request->reminder_datetime,
                'reminder_remarks' => $request->reminder_remarks
            ]);
        }

        // Trigger the 'booking_created' event for the given enquiry ID using the custom helper function
        $data_data = Helper::Event_Triggerd(['booking_created'], $enquiry->id);

        return response()->json(['message' => 'Enquiry created', 'data' => $enquiry->load(['client', 'project', 'status', 'assignedTo', 'source', 'properties'])], 201);
    }

    public function show(Enquiry $enquiry)
    {
        return response()->json($enquiry->load(['client', 'project', 'status', 'assignedTo', 'source', 'properties']));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'client_id' => 'exists:clients,id',
            'project_id' => 'exists:projects,id',
            'enquiry_status_id' => 'exists:enquiry_statuses,id',
            'rating' => 'string|nullable',
            'general_remarks' => 'string|nullable',
            'assign_to_id' => 'exists:users,id|nullable',
            'source_id' => 'exists:enquiry_sources,id|nullable',
            'source_remarks' => 'string|nullable',
            'followup_notes' => 'string|nullable',
            'closure_reason' => 'string|nullable',
            'junk_status_name' => 'string|nullable',
            'junk_reason' => 'string|nullable',
            'reminder_datetime' => 'date|nullable',
            'reminder_remarks' => 'string|nullable',
            'property_ids' => 'array|nullable',
            'property_ids.*' => 'exists:properties,id',
        ]);

        $enquiry->update($request->except('property_ids'));

        if ($request->property_ids) {
            $enquiry->properties()->sync($request->property_ids);
        }

        $data_data = Helper::Event_Triggerd(['enquiry_created'], $enquiry->id);

        return response()->json(['message' => 'Enquiry updated', 'data' => $enquiry->load(['client', 'project', 'status', 'assignedTo', 'source', 'properties'])]);
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();
        return response()->json(['message' => 'Enquiry deleted']);
    }

    public function assign(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'assign_to_id' => 'required|exists:users,id',
        ]);

        $enquiry->update(['assign_to_id' => $request->assign_to_id]);

        return response()->json(['message' => 'Enquiry assigned', 'data' => $enquiry->load(['assignedTo'])]);
    }

    public function attachProperties(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'property_ids' => 'required|array',
            'property_ids.*' => 'exists:properties,id',
        ]);

        $enquiry->properties()->syncWithoutDetaching($request->property_ids);

        return response()->json(['message' => 'Properties attached', 'data' => $enquiry->load('properties')]);
    }

    public function junk(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'junk_status_name' => 'required|string|max:255',
            'reason' => 'string|nullable',
        ]);

        $enquiry->update([
            'junk_status_name' => $request->junk_status_name,
            'junk_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Enquiry marked as junk', 'data' => $enquiry]);
    }

    public function close(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'final_status_name' => 'required|string|max:255',
            'reason' => 'string|nullable',
        ]);

        $enquiry->update([
            'enquiry_status_id' => EnquiryStatus::where('name', $request->final_status_name)->firstOrFail()->id,
            'closure_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'Enquiry closed', 'data' => $enquiry]);
    }

    public function followup(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'followup_notes' => 'required|string',
        ]);

        $enquiry->update(['followup_notes' => $request->followup_notes]);

        return response()->json(['message' => 'Follow-up notes updated', 'data' => $enquiry]);
    }

    public function reminder(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'id' => 'nullable|exists:reminders,id',
            'reminder_datetime' => 'required|date',
            'reminder_remarks' => 'string|nullable',
        ]);

        if ($request->id) {
            $enquiry->reminders()->where('id', $request->id)->update([
                'reminder_datetime' => $request->reminder_datetime,
                'reminder_remarks' => $request->reminder_remarks,
            ]);
        } else {
            $enquiry->reminders()->create([
                // 'enquiry_id' => $enquiry->id,
                'reminder_datetime' => $request->reminder_datetime,
                'reminder_remarks' => $request->reminder_remarks
            ]);
        }

        return response()->json(['message' => 'Reminder updated', 'data' => $enquiry]);
    }

    public function TestFunction(Request $request)
    {
        $data = '1';
        $data_data = Helper::Event_Triggerd(['booking_created'], $data);
    }

    public function reminders(Request $request)
    {
        $startDate = $request->reminder_date_from;
        $endDate = $request->reminder_date_to;

        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        $reminders = Enquiry::with(['reminders'])
            ->whereHas('reminders', function ($query) use ($startDate, $endDate) {
                $query->whereDate('reminder_datetime', '>=', $startDate)
                    ->whereDate('reminder_datetime', '<=', $endDate);
            })
            ->where('is_completed', 0)
            ->get();

        return response()->json($reminders);
    }

    public function complete(Request $request, Enquiry $enquiry, EnquiryReminder $reminder)
    {
        $request->validate([
            'completed_remarks' => 'required|string',
        ]);

        $reminder->update([
            'is_completed' => 1,
            'completed_remarks' => $request->completed_remarks
        ]);

        return response()->json(['message' => 'Enquiry Reminder Completed.', 'data' => $reminder]);
    }
}