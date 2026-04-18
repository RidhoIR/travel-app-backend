<?php


namespace App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AdminScheduleController extends Controller
{
    public function index(Request $request)
    {
        $q = Schedule::with('destination');
        if ($request->filled('date'))           $q->where('date', $request->date);
        if ($request->filled('destination_id')) $q->where('destination_id', $request->destination_id);
        $list = $q->orderBy('date')->orderBy('time_slot')->get()->map(fn($s) => $this->fmt($s));
        return response()->json(['data' => $list]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'destination_id' => 'required|integer|exists:destinations,id',
            'date'           => 'required|date',
            'time_slot'      => 'required|string|max:50',
            'quota'          => 'required|integer|min:1|max:1000',
            'price'          => 'required|numeric|min:0',
        ]);
        $s = Schedule::create($data + ['booked' => 0, 'is_available' => true]);
        return response()->json(['message' => 'Slot dibuat!', 'data' => $this->fmt($s->load('destination'))], 201);
    }

    public function update(Request $request, int $id)
    {
        $s    = Schedule::findOrFail($id);
        $data = $request->validate([
            'date'         => 'sometimes|date',
            'time_slot'    => 'sometimes|string|max:50',
            'quota'        => 'sometimes|integer|min:1',
            'price'        => 'sometimes|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        $s->update($data);
        return response()->json(['message' => 'Slot diperbarui!', 'data' => $this->fmt($s->fresh()->load('destination'))]);
    }

    public function destroy(int $id)
    {
        $s = Schedule::findOrFail($id);
        if ($s->booked > 0)
            return response()->json(['message' => 'Slot sudah ada booking, tidak bisa dihapus.'], 422);
        $s->delete();
        return response()->json(['message' => 'Slot dihapus.']);
    }

    private function fmt(Schedule $s): array
    {
        return [
            'id'             => $s->id,
            'destination_id' => $s->destination_id,
            'destination'    => $s->destination ? [
                'id' => $s->destination->id, 'name' => $s->destination->name,
                'image_url' => $s->destination->image_url,
            ] : null,
            'date'           => $s->date->format('Y-m-d'),
            'time_slot'      => $s->time_slot,
            'quota'          => $s->quota,
            'booked'         => $s->booked,
            'price'          => $s->price,
            'is_available'   => $s->is_available,
        ];
    }
}
