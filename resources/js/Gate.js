export default	class Gate {

	// Every class in a js must have a constructor;
	constructor(user) {
		this.user = user;
	}

	isAdmin(){
		return this.user.type === "admin";
	}
	
	isUser(){
		return this.user.type === "user";
	}
	
	isAuther(){
		return this.user.type === "author";
	}

	isAdminOrAuthor(){
		if (this.user.type === "admin" || this.user.type === "author") {
			return true;
		}
		
	}

	isAuthorOrUser(){
		if (this.user.type === "author" || this.user.type === "user") {
			return true;
		}
		
	}

}