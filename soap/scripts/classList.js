document.addEventListener('DOMContentLoaded', function () {
    const rest2ApiUrl = document.getElementById('rest2ApiUrl').getAttribute('data-api-url');
    const tableBody = document.getElementById('class-list');

    fetch(`${rest2ApiUrl}classes`)
        .then(response => response.json())
        .then(response => {
            let html = '';

            if (response.status === '200' && response.data.length > 0) {
                response.data.forEach(function (classItem) {
                    html += `
                        <tr>
                            <td class="px-4 py-2">${classItem.name}</td>
                            <td class="px-4 py-2">${classItem.start}</td>
                            <td class="px-4 py-2">${classItem.end}</td>
                            <td class="px-4 py-2">${classItem.capacity}</td>
                            <td class="px-4 py-2">${classItem.quantity}</td>
                            <td class="px-4 py-2">${classItem.member_only === 1 ? 'Yes' : 'No'}</td>
                            <td class="px-4 py-2">
                                <a href="class_detail.php?id=${classItem.id}" class="bg-green-500 text-white px-2 py-1 rounded">Detail</a>
                                <a href="edit_class.php?id=${classItem.id}" class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</a>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="7" class="text-center text-red-500">${response.message || 'No classes available.'}</td></tr>`;
            }

            tableBody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching classes:', error);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-red-500">Error loading classes.</td></tr>`;
        });
});
