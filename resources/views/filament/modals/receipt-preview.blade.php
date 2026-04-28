<div class="flex flex-col items-center">
    <div class="w-full bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700" style="height: 500px;">
        <iframe 
            id="receipt-iframe"
            src="{{ $url }}" 
            class="w-full h-full border-none"
            title="Pratinjau Kwitansi"
        ></iframe>
    </div>

    <script>
        document.addEventListener('print-receipt', () => {
            const iframe = document.getElementById('receipt-iframe');
            if (iframe) {
                iframe.contentWindow.print();
            }
        });
    </script>
</div>
