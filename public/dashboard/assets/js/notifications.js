function getNotifications (url = notification_url)
{
      fetch(url)
        .then(function (response) {
          if (response.status === 200) {
            return response.json();
          } else {
            throw new Error('Failed to fetch data');
          }
        })
        .then(function (data) {
          loadNotification(data)
        })
        .catch(function (error) {
          console.error(error);
        });
}

function loadNotification(data)
{
    let notifications_count = 0;
    let notification_elements = '';
    let notification_container = document.getElementById("notification_container");
    let notification_badge_number = document.getElementById("notification-badge-number");
  

    data.forEach(function(notification) 
    {
        date = new Date(notification.created_at);

        formattedDate = date.toLocaleDateString('en-US');
        
        notification_elements += `<li class="noti-secondary">
                                    <div class="media">
                                        <div class="media-body">
                                                <p class='notification-item' id='${notification.id}'>${notification.data.message}</p><span>${formattedDate}</span>
                                        </div>
                                    </div>
                                </li>`;
        notifications_count ++;
    }); 
      notification_container.innerHTML = `<li>
                    <a href='${notification_page}' class="f-w-700 mb-0">You have ${notifications_count} Notifications<span class="pull-right badge badge-primary badge-pill">${notifications_count}</span></a>
                  </li>`;
    notification_container.innerHTML += notification_elements;
    notification_badge_number.textContent = notifications_count;
    
}

window.onload = function () {

    getNotifications()
    
   document.addEventListener('click', function (event) {
    const clickedElement = event.target;
    const className = clickedElement.className;
    if (className === 'notification-item') {
        getNotifications (notification_make_read + '/' + clickedElement.id)
    } 
});
};
