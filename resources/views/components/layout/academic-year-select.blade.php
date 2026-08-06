@props([
    'academicYears' => collect(),
    'currentAcademicLabel' => null,
])

<div class="hidden md:block">
    <select
        id="academicYearSelect"
        class="h-11 cursor-pointer appearance-none rounded-full border border-white/16 bg-white/10 pl-4 pr-10 text-xs font-semibold text-white shadow-sm transition hover:bg-white/16 focus:outline-none focus:ring-2 focus:ring-white/20"
        style="background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%23FFFFFF' stroke-opacity='0.85' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 14px center"
    >
        <option value="" class="text-slate-900">Aujourd'hui</option>

        @foreach($academicYears as $academicYear)
            @php
                $parts = explode('-', $academicYear->libelle);
                $valueDate = ($parts[0] ?? '') . '-09-01';
            @endphp

            <option value="{{ $valueDate }}" class="text-slate-900" {{ $currentAcademicLabel === $academicYear->libelle ? 'selected' : '' }}>
                {{ $academicYear->libelle }}
            </option>
        @endforeach
    </select>
</div>
