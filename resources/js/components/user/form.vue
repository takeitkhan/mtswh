<template>
    <div>
        <div class="contact-form_wrap">

            <div class="row">
                    <div class="col-md-8 col-lg-3 col-sm-12 m-auto">
                        <div class="form-content">
                                <div class="form-group name">
                                    <label for="name">Name: </label>
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" v-model="form.name">
                                </div>
                                <div class="form-group email">
                                    <label for="email">Email: </label>
                                    <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" v-model="form.email">
                                </div>
                                <div class="form-group">
                                    <label for="phoneNumber">Phone Number: </label>
                                    <input type="number" class="form-control" id="phoneNumber" placeholder="Phone number" v-model="form.phone">
                                </div>
                                <div class="form-group select arrow_class">
                                    <label for="select">Select Role </label>
                                    <select class="form-select select-box" aria-label=".form-select-lg example" id="select">
                                        <option value="1">Super Admin</option>
                                        <option value="2">Admin</option>
                                        <option value="3">User</option>
                                    </select>
                                </div>
                                <div class="form-submit_btn">
                                    <button type="submit" class="btn blue" v-on:click="submit()">Submit</button>
                                </div>
                        </div>
                    </div>
                </div>



        </div>
    </div>
</template>


<script>

export default {
    data : function(){
        return {
            form: {
                name: '',
                email: '',
                phone: '',
            }
        }
    },
    methods: {
        submit(){
            axios.post('user/store', this.form)
                .then(function(response){
                    // Handle success
                    console.log(response);
                }.bind(this));
        }
    }
}
</script>