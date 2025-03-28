 /*INLCUDE THIS SCRIPT TAG AT THE TOP OF HTML FILE FOR JQUERY!!!!! <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> */

//Get modal element

var modal = document.getElementById('simpleModal');

//Get open modal button
var modalBtn = document.getElementById('modalBtn');

//Get close button
var closeBtn = document.getElementsByClassName('closeBtn')[0];

//Get the modal header to display customer when it is selected
var modalheader = document.getElementById('modalheader');

//Listen for click
modalBtn.addEventListener('click', openModal);

//Listen for close click
closeBtn.addEventListener('click', closeModal);

//Listen for outside click
window.addEventListener('click',outsideclick);

//function to open modal
function openModal()
{
  modal.style.display = 'block';
}

function closeModal()
{
    modal.style.display = 'none';
}
//function to close modal if outside click
function outsideclick(e)
{
    if(e.target == modal){
		$(modal).find("input[type=text]").val("");
            modal.style.display = 'none';
                        }
}

$('#simpleModal').on('hidden.bs.modal', function (e) {
    // Clear the input field when the modal is closed
    $(this).find("input[type=text]").val("");
  });

  // Event handler for when the close button is clicked
  $('.closeBtn').on('click', function () {
    // Trigger the "hidden.bs.modal" event manually
    $('#simpleModal').trigger('hidden.bs.modal');
  });
		

