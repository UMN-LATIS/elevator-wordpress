// https://content.pivotal.io/blog/javascript-constructors-prototypes-and-the-new-keyword

var elevatorChooser = function ElevatorChooser(targetButton, callback) {
  this.targetButton = targetButton;
  this.callback = callback;
}

// Instance methods
elevatorChooser.prototype = {
  launchWindow: function launchWindow() {
  	alert("launched!")
  }

}