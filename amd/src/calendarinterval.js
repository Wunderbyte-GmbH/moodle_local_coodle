
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * Modal Form user create
 * @package    local_coodle
 * @copyright  Wunderbyte GmbH <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
    // Get the select element
    // Get the button that opens the modal
    // var btn = document.querySelector('[data-action="new-event-button"]');

    // // When the user clicks on the button, open the modal
    // btn.onclick = function() {

    // // Create a new MutationObserver instance to observe changes to the DOM
    // var observer = new MutationObserver(function(mutations) {
    //     // Loop through the mutations and check if the modal element has been added
    //     mutations.forEach(function(mutation) {

    //     var modal = mutation.target.querySelector(".modal");
    //     if (modal) {
    //         // If the modal element has been added, disconnect the observer and call the myFunction() function
    //         observer.disconnect();
    //         changeminuteinterval();
    //     }
    //     });
    // });
    //  // Set the observer to watch for changes to the body element
    //  observer.observe(document.body, { childList: true, subtree: true });
    // };

    var buttons = Array.from(document.querySelectorAll('.maincalendar .clickable'));
    buttons.push(document.querySelector('[data-action="new-event-button"]'));
    // Loop through each button and attach a click event listener
    buttons.forEach(function(btn) {
        btn.onclick = function() {

            // Create a new MutationObserver instance to observe changes to the DOM
            var observer = new MutationObserver(function(mutations) {
                // Loop through the mutations and check if the modal element has been added
                mutations.forEach(function(mutation) {
                    var modal = mutation.target.querySelector(".modal");
                    if (modal) {
                        // If the modal element has been added, disconnect the observer and call the changeminuteinterval() function
                        observer.disconnect();
                        changeminuteinterval();
                    }
                });
            });

            // Start observing the DOM changes
            observer.observe(document.body, { childList: true, subtree: true });
        };
    });
};

// Function that should be triggered after the modal content is loaded
const changeminuteinterval = () => {
  // Add your code here

  // Wait until the select element is visible
  var intervalId = setInterval(function() {
    var select = document.getElementById('id_timestart_minute');
    if (select && select.offsetParent !== null) {
      clearInterval(intervalId);

      // Loop through the options and remove the ones that are not 15, 30, or 45
      for (let i = select.options.length - 1; i >= 0; i--) {
        const value = parseInt(select.options[i].value);
        if (![0, 15, 30, 45].includes(value)) {
          select.remove(i);
        }
      }
    }
  }, 100);
};
