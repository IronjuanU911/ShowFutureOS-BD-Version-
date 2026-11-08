@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#511560] focus:ring-[#511560] rounded-md shadow-sm']) }}>
