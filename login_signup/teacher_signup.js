async function generateTeacherUserId() {
    let userId;
    do {
        userId = 'T' + Math.floor(1000 + Math.random() * 9000);
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

document.getElementById('teacher-form').addEventListener('submit', async function(event) {
    event.preventDefault();
    const password = document.getElementById('teacher-password').value;
    const re_pass = document.getElementById('teacher-re_pass').value;

    if (password !== re_pass) {
        alert("Passwords do not match!");
        return;
    }

    const userId = await generateTeacherUserId();
    document.getElementById('teacher-user_id').value = userId;
    //alert("Done");
    document.getElementById('teacher-form').submit();
    //alert("Done");
});
