// script.js
document.getElementById("registrationForm").addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const passwordError = document.getElementById("passwordError");

    if (password !== confirmPassword) {
        // แสดงข้อความผิดพลาด
        passwordError.textContent = "Passwords do not match!";
        event.preventDefault(); // หยุดการส่งฟอร์ม
    } else {
        passwordError.textContent = ""; // ล้างข้อความผิดพลาด
        alert("Registration successful!");
    }
});
