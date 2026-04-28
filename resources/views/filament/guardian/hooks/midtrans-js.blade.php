@php
    $isProduction = config('services.midtrans.is_production');
    $snapUrl = $isProduction 
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script type="text/javascript"
        src="{{ $snapUrl }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
    let isSnapLoading = false;

    const handleSnapPopup = (snapToken) => {
        if (isSnapLoading) return;
        
        if (!window.snap) {
            console.error('Midtrans Snap is not loaded. Retrying in 500ms...');
            setTimeout(() => handleSnapPopup(snapToken), 500);
            return;
        }

        isSnapLoading = true;

        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                isSnapLoading = false;
                window.location.reload();
            },
            onPending: function(result) {
                isSnapLoading = false;
                window.location.reload();
            },
            onError: function(result) {
                isSnapLoading = false;
                console.error('Payment error:', result);
            },
            onClose: function() {
                isSnapLoading = false;
                console.log('Customer closed the popup');
            }
        });
    };

    // Global listener for Livewire 3 browser events
    window.addEventListener('open-midtrans-snap', event => {
        // Livewire 3 standard parameters are in event.detail
        const snapToken = event.detail?.snapToken || (Array.isArray(event.detail) ? event.detail[0]?.snapToken : null);
        if (snapToken) {
            handleSnapPopup(snapToken);
        }
    });
</script>


