this.openModal = async () => {
    this.AlertController.create({header: 'Hallo Welt', message:
    <ion-toolbar>
      <ion-item lines="none">
        <ion-input placeholder="Type here" [(ngModel)]="todo"></ion-input>
        <ion-button slot="end" name="local_coodle_add_todo" refreshOnSuccess="true"
        core-site-plugins-call-ws [params]="{userid: <%userid%>, todo: todo}">Submit</ion-button>
      </ion-item>
    </ion-toolbar>'}).then(alert => alert.present());
}