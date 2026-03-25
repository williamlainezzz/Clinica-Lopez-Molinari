<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#1B4D3E] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#153a2f] focus:bg-[#153a2f] active:bg-[#0e271f] focus:outline-none focus:ring-2 focus:ring-[#1B4D3E] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
