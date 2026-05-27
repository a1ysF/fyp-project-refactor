
document.getElementById('materialForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const typeSelect = document.getElementById('type').value;
    // const dateSubmittedInput = document.getElementById('dateSubmitted').value;
    // const dateEditedInput = document.getElementById('dateEdited').value;

    const selectedType = typeSelect;

    let prefix = '';

    switch (selectedType) {
        case 'Learning':
            prefix = 'L';
            break;
        case 'Assignment':
            prefix = 'A';
            break;
        case 'Quiz':
            prefix = 'Q';
            break;
        default:
            prefix = '';
    }

    if (prefix) {
        const materialIDInput = await generateUniqueMaterialId(prefix);
        document.getElementById('materialID').value = materialIDInput;
    }

    // Set the current date and time for dateSubmitted and dateEdited
    // const currentDateTime = new Date().toISOString();
    // document.getElementById('dateSubmitted').value = currentDateTime;
    // document.getElementById('dateEdited').value = currentDateTime;

    document.getElementById('materialForm').submit();
});

async function checkMaterialId(materialId) {
    const response = await fetch('check_material_id.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ material_id: materialId })
    });
    const data = await response.json();
    return data.exists;
}

async function generateUniqueMaterialId(prefix) {
    let materialID = '';
    let exists = true;

    while (exists) {
        const randomNumber = Math.floor(1000 + Math.random() * 9000); // Generates a random 4-digit number
        materialID = `${prefix}${randomNumber}`;
        exists = await checkMaterialId(materialID);
    }

    return materialID;
}
