@extends('layouts.hnplus')

@section('content')
<div class="container mt-4">
    <button class="btn btn-danger" id="gitPullBtn" style="display: inline;">Git Pull</button> 

    <pre id="gitOutput" style="background: #eeee; padding: 1rem; margin-top: 1rem;"></pre>    

    <!-- แจ้ง Git Pull -->
    <script>
        document.getElementById('gitPullBtn').addEventListener('click', function () {
            if (!confirm("คุณแน่ใจว่าจะ Git Pull ใช่ไหม?")) return;

            let outputBox = document.getElementById('gitOutput');
            outputBox.textContent = 'กำลังดำเนินการ...';

            fetch("{{ route('admin.git.pull') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                outputBox.textContent = data.output || data.error || 'ไม่มีข้อมูล';
                // ตรวจสอบว่า git pull สำเร็จหรือไม่
                if (data.output && data.output.includes('Updating') || data.output.includes('Already up to date')) {
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.main_setting') }}"; // เปลี่ยนเป็น route ที่คุณต้องการ redirect ไป
                    }, 5000); // รอ 5 วินาทีก่อน redirect
                }
            })
            .catch(error => {
                outputBox.textContent = "เกิดข้อผิดพลาด: " + error;
            });
        });
    </script>  
</div>

@endsection