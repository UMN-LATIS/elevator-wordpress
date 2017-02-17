
 (function ( $ ) {
  "use strict";

  $(function () {



    function ElevatorChooser(setupOptions) {
      var self = this;
      this.openedWindow = null;
      this.targetButton = document.getElementById(setupOptions.targetElement);
      this.targetButton.addEventListener('click', function(event) { self.launchWindow()});
      this.targetInstance = setupOptions.targetInstance;
      this.callback = setupOptions.callback;
      this.pluginType = setupOptions.pluginType;
      this.apiKey = setupOptions.apiKey;
      this.entangledSecret = setupOptions.entangledSecret;
      this.includeMetadata = setupOptions.includeMetadata;
      this.includeLink = setupOptions.includeLink;
      this.timeStamp = setupOptions.timeStamp;

      window.addEventListener("message", function(event) { self.recievedMessage(event); }, false);

    }

// Instance methods
ElevatorChooser.prototype = {
  launchWindow: function launchWindow() {
    this.openedWindow = window.open(this.targetInstance, "elevatorPlugin");
  },
  recievedMessage: function recievedMessage(message) {
    if(message.data == "parentLoaded") {
      this.openedWindow.postMessage(
      {
        pluginSetup: true, 
        elevatorPlugin:this.pluginType, 
        includeMetadata: this.includeMetadata,
        elevatorCallbackType:"JS", 
        apiKey: this.apiKey, 
        timeStamp:this.timeStamp, 
        entangledSecret: this.entangledSecret
      }, "*");  
    }
    if(typeof message.data.pluginResponse !== "undefined") {
      console.log(message.data);
      this.callback(message.data);
    }
    console.log(message);
  }

}




$(document).ready(function(){
            //console.log('hi there2');
            var endpoint = $("#elevatorchooserbtn").data("endpoint");
            var includeSummary = $("#elevatorchooserbtn").data("includesummary");
            var includeLink = $("#elevatorchooserbtn").data("includelink");
            var elevatorButton = new ElevatorChooser({
              targetElement: "elevatorchooserbtn",
              targetInstance: endpoint,
              pluginType: "WordPress",
              includeMetadata: includeSummary,
              includeLink: includeLink,
              callback: function(e) { 
                var shortcode = '[elevator width=640 height=480 includelink="' + includeLink +'" includesummary="'+includeSummary+'" fileobjectid="' + e.fileObjectId + '" objectid="' + e.objectId + '" sourceurl="' + e.currentLink + '"]';
                
                wp.media.editor.insert(shortcode);
              },
              apiKey: '',
              entangledSecret: '',
              timeStamp: ''
            });



            

          });

});

}(jQuery));

