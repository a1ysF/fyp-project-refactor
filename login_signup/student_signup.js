// Age calculation for student
function calculateStudentAge() {
    const day = document.getElementById('student-day').value;
    const month = document.getElementById('student-month').value;
    const year = document.getElementById('student-year').value;

    if (day && month && year) {
        const dob = new Date(year, month - 1, day);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDifference = today.getMonth() - dob.getMonth();
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('student-age').value = age;
    }
}

document.getElementById('student-day').addEventListener('change', calculateStudentAge);
document.getElementById('student-month').addEventListener('change', calculateStudentAge);
document.getElementById('student-year').addEventListener('change', calculateStudentAge);

async function generateStudentUserId() {
    let userId;
    do {
        userId = 'S' + Math.floor(1000 + Math.random() * 9000);
    } while (await checkUserId(userId));
    return userId;
}

async function checkUserId(userId) {
    const response = await fetch('check_user_id.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user_id: userId })
    });
    const data = response.json();
    return data.exists;
}

document.getElementById('student-form').addEventListener('submit', async function(event) {
    event.preventDefault();
    const password = document.getElementById('student-password').value;
    const re_pass = document.getElementById('student-re_pass').value;

    if (password !== re_pass) {
        alert("Passwords do not match!");
        return;
    }

    const userId = await generateStudentUserId();
    document.getElementById('student-user_id').value = userId;
    document.getElementById('student-form').submit();
});