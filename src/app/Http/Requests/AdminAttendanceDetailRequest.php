<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'work_start' => 'required|date_format:H:i',
            'work_end'   => 'required|date_format:H:i',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end'   => 'nullable|date_format:H:i',
            'text' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'work_start.required' => '出勤時間を入力してください',
            'work_end.required'   => '退勤時間を入力してください',
            'work_start.date_format' => '時間は HH:MM 形式で入力してください',
            'work_end.date_format'   => '時間は HH:MM 形式で入力してください',
            'breaks.*.start.date_format' => '時間は HH:MM 形式で入力してください',
            'breaks.*.end.date_format'   => '時間は HH:MM 形式で入力してください',
            'text.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (
                !$this->filled('work_start') ||
                !$this->filled('work_end') ||
                $validator->errors()->has('work_start') ||
                $validator->errors()->has('work_end')
            ) {
                return;
            }

            $workStart = Carbon::createFromFormat('H:i', $this->work_start);
            $workEnd   = Carbon::createFromFormat('H:i', $this->work_end);

            if ($workStart->gt($workEnd)) {
                $validator->errors()->add(
                    'work_start',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->breaks ?? [] as $index => $break) {

                if (
                    empty($break['start']) ||
                    $validator->errors()->has("breaks.$index.start")
                ) {
                    continue;
                }

                $breakStart = Carbon::createFromFormat('H:i', $break['start']);

                if ($breakStart->lt($workStart) || $breakStart->gt($workEnd)) {
                    $validator->errors()->add(
                        "breaks.$index.start",
                        '休憩時間が不適切な値です'
                    );
                }

                if (
                    !empty($break['end']) &&
                    !$validator->errors()->has("breaks.$index.end")
                ) {
                    $breakEnd = Carbon::createFromFormat('H:i', $break['end']);

                    if ($breakStart->gt($breakEnd)) {
                        $validator->errors()->add(
                            "breaks.$index.start",
                            '休憩時間が不適切な値です'
                        );
                        continue;
                    }

                    if ($breakEnd->gt($workEnd)) {
                        $validator->errors()->add(
                            "breaks.$index.end",
                            '休憩時間もしくは退勤時間が不適切な値です'
                        );
                    }
                }
            }
        });
    }
}
