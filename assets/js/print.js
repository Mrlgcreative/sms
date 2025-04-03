<script>
        function printContent(elementId) {
            var content = document.getElementById(elementId).innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = content;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>