{{-- resources/views/widgets/dealership-label.blade.php --}}
<div class="gm-info-window" style="font-family: Arial, sans-serif; max-width: 250px;">
    @if(!empty($dealershipIcon))
    <div style="text-align: center; margin-bottom: 8px;">
        <img src="{{ $dealershipIcon }}"
            alt="{{ $dealershipName }}"
            style="max-height: 50px; max-width: 100%; border-radius: 4px;">
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
            <a href="{{ route('/view', $dealershipId) }}"
                style="display: inline-block; padding: 6px 12px; background: #1a73e8; 
                      color: white; text-decoration: none; border-radius: 4px; font-size: 13px;">
                View Details
            </a>
        </div>
    </div>
</div>