<?php

namespace App\Http\Controllers;

use App\Models\Contact;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ContactController extends Controller
{
    public function getData(Request $request)
    {
        $query = \App\Models\Contact::query()
            ->where('Status', 1);

        if ($request->filled('station_id')) {
            $query->where('StationID', $request->station_id);
        }

        return DataTables::of($query)->make(true);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'StationID' => 'required|integer',
            'CustomerID' => 'nullable|integer',
            'ContactName' => 'required|string|max:255',
            'ContactEmail' => 'required|email|max:255',
            'ContactPhone' => 'required|string|max:50',
        ]);

        $contact = Contact::create([
            'StationID' => $validated['StationID'],
            'CustomerID' => $validated['CustomerID'] ?? null,
            'ContactName' => $validated['ContactName'],
            'ContactEmail' => $validated['ContactEmail'],
            'ContactPhone' => $validated['ContactPhone'],
            'Status' => 1,
            'created_by' => auth()->user()->id ?? 1, // ถ้าใช้ auth
            'updated_by' => auth()->user()->id ?? 1,
        ]);

        return response()->json(['success' => true, 'contact' => $contact]);
    }
    public function destroy($id)
{
    
    $contact = Contact::find($id);

    if (!$contact) {
        return response()->json([
            'success' => false,
            'message' => 'ไม่พบผู้ติดต่อ'
        ]);
    }

    $contact->Status = '0'; // 👈 อัปเดต Status เป็น "จ"
    $contact->updated_by = auth()->user()->id ?? 1;
    $contact->save();

    return response()->json(['success' => true]);
}
}
