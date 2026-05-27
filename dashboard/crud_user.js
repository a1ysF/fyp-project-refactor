document.addEventListener('DOMContentLoaded', function () {
    const dayInput = document.getElementById('birthdateDay');
    const monthInput = document.getElementById('birthdateMonth');
    const yearInput = document.getElementById('birthdateYear');
    const form = document.querySelector('form');
    const hiddenAgeInput = document.createElement('input');
    hiddenAgeInput.type = 'hidden';
    hiddenAgeInput.name = 'age';
    form.appendChild(hiddenAgeInput);

    function calculateAge(day, month, year) {
        const today = new Date();
        const birthDate = new Date(year, month - 1, day);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDifference = today.getMonth() - birthDate.getMonth();
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    function updateAge() {
        const day = parseInt(dayInput.value);
        const month = parseInt(monthInput.value);
        const year = parseInt(yearInput.value);

        if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
            const age = calculateAge(day, month, year);
            hiddenAgeInput.value = age;
            console.log('Calculated Age:', age); // For debugging
        }
    }

    dayInput.addEventListener('input', updateAge);
    monthInput.addEventListener('input', updateAge);
    yearInput.addEventListener('input', updateAge);

    form.addEventListener('submit', function (event) {
        updateAge(); // Ensure age is calculated before submitting
    });
});
