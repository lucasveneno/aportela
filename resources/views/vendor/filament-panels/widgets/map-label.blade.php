{{-- resources/views/widgets/dealership-label.blade.php --}}
<div class="gm-info-window" style="font-family: Arial, sans-serif; max-width: 250px;">
    @if(empty($dealershipIcon))
    <div style="text-align: center; margin-bottom: 8px;">


        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
        </svg>

    </div>
    @endif

    <div style="padding: 0 8px;">
        <h3 style="margin: 0 0 5px 0; font-size: 16px; color: #1a73e8;">
            {{ $dealershipName }}
        </h3>

        <div style="display: flex; align-items: center; margin-bottom: 5px;">
            <span style="font-size: 12px; color: #5f6368; margin-right: 5px;">ID:</span>
            <span style="font-size: 13px; font-weight: 500;">{{ $dealershipId }}</span>
        </div>

        <div style="margin-top: 10px; text-align: center;">
            <a href="https://projus.app/app/demands/{{ $dealershipId }}/view"
                style="display: inline-block; padding: 6px 12px; background: #1a73e8; 
                      color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                View Details
            </a>
        </div>
    </div>
</div>