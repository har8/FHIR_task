$('.fetch-data-btn').click(function() {
    var key = $(this).data('key');
    var app = $(this).data('app');
    var extractBtnElement = $(this).prev().prev();
	var textArea = $(this).next();
    $.ajax({
        url: '/' + app + '-fhir/fetch/'+key,
        method: 'GET',
        success: function(response) {
            textArea.val(response);
            extractBtnElement.removeClass('hidden');
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});


$('.extract-data-btn').click(function() {
    const ndJsonData = $(this).next().next().next().val();
    const data = ndJsonData.split(/\r?\n/);

    data.forEach(line => {
        try {
          const patientData = JSON.parse(line);
          // Extract the required information from patientData and do further processing
          const id = patientData.id;
          const gender = patientData.gender;
          const birthDate = patientData.birthDate;
          const firstName = patientData.name[0].given[0];
          const lastName = patientData.name[0].family;
          const fullName = `${firstName} ${lastName}`;
          
          const row = "<tr><td>" + id +  "</td><td>" + fullName + "</td><td>" + birthDate + "</td><td>" + gender + "</td></tr>";
          
          $(this).next().find("table > tbody").append(row);
          console.log("Gender:", gender);
          console.log("Birthdate:", birthDate);
          console.log("Name:", fullName);
        } catch (error) {
          console.error("Error parsing JSON:", error);
        }
        $(this).next().removeClass('hidden');
      });
});
