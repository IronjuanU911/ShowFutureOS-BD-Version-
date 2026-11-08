<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#511560] hover:bg-[#41104d] active:bg-[#2c0b34] border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-[#511560] focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
