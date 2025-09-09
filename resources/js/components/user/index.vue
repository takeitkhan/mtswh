<template>
    <div>
         <div class="products-list-table desktop-view xmobile-view">
            
            <table id="table_id">
                <thead style="position: sticky;top:-1px;">
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee No</th>
                        <th>Phone</th>
                        <th>Employee status</th>

                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user of users" v-bind:key="user.id">
                        <td>
                            <a href="javascript:void(0)">
                                <i class="fas fa-times"></i>
                            </a>
                            <a href="javascript:void(0)">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="javascript:void(0)">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                        <td> {{ user . name }} </td>
                        <td> {{ user . email }} </td>
                        <td> {{ user . phone }} </td>
                        <td> {{ user . employee_no }} </td>
                        <td> {{ user . employee_status }} </td>

                    </tr>


                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data: function()  {
            return {
                users: {},
            }
        },
        methods: {
            getUser: function() {
                let app = this;
                axios.get("user/api").then(function(response) {
                        app.users = response.data;
                    });
            },
        },
        mounted: function() {
          this.getUser();
        }
    }
</script>