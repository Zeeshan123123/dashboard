<template> 
    <!-- Template tag should have only one child Therefore modal is not in template tag -->
    <div class="container">
        <div class="row" v-if="$gate.isAdmin()">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Manage User</h3>

                <div class="card-tools">
                  <button class="btn btn-success" @click="newModal">Add new <i class="fas fa-user-plus fa-fw"></i> </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registered At</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{ user.id }}</td>
                      <td>{{ user.name }}</td>
                      <td>{{ user.email }}</td>
                      <td>{{ user.type | upText }}</td>
                      <td>{{ user.created_at | myDate }}</td>
                      <td>
                          <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit"></i>
                          </a>
                           /
                          <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                          </a>
                      </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <pagination :data="users" @pagination-change-page="getResults">
                  <!-- @ sign in vue.js refers to event -->
                </pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div>
          <not-found v-if="!$gate.isAdminOrAuthor()"></not-found>
        </div>

        <!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 v-show="!editmode" class="modal-title" id="addNewLabel">Add New</h5>
        <h5 v-show="editmode" class="modal-title" id="addNewLabel">Update User's Info</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="editmode ? updateUser() : createUser()">
      <div class="modal-body">
        <div class="form-group">
          <input v-model="form.name" type="text" name="name"
            placeholder="Name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
          <has-error :form="form" field="name"></has-error>
        </div>

        <div class="form-group">
          <input v-model="form.email" type="email" name="email"
            placeholder="Email Address" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
          <has-error :form="form" field="email"></has-error>
        </div>

        <div class="form-group">
          <textarea v-model="form.bio" type="text" name="bio" id="bio" 
            placeholder="Short bio of user (optional)" class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
          <has-error :form="form" field="bio"></has-error>
        </div>

        <div class="form-group">
          <select v-model="form.type" name="type" id="type" 
            class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
            <option value="">Select User Role</option>
            <option value="admin">Admin</option>
            <option value="user">Standard User</option>
            <option value="author">Author</option>
           </select>
          <has-error :form="form" field="type"></has-error>
        </div>

        <div class="form-group">
          <input v-model="form.password" type="password" name="password" id="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
          <has-error :form="form" field="password"></has-error>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
      </div>
      </form>
    </div>
  </div>
</div>

</div>
</template>

<script>
    export default {
        data() {
            return {
                editmode: false, // check if editmode false than display add function otherwise edit function;
                users: {},
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
                })
            }
        },

        methods: {

            getResults(page = 1) {
              axios.get('api/user?page=' + page)
                .then(response => {
                  this.users = response.data;
                });
            },
            updateUser(){
              this.$Progress.start();
              this.form.put('api/user/'+this.form.id)
              .then( () => {
                $('#addNew').modal('hide');
                Swal.fire(
                  'Updated!',
                  'Information has been updated.',
                  'success'
                )
                this.$Progress.finish();
                Fire.$emit('AfterCreate');
              })
              .catch( () => {
                this.Progress.fail();
              });
            },
            editModal(user) {
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(user);
            },
            newModal() {
              this.editmode = false;
              this.form.reset();
              $('#addNew').modal('show');
            },
            deleteUser(id) {
                Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    // Send request to the server;
                    if (result.value) {
                    this.form.delete('api/user/'+id).then( () => {
                            Swal.fire(
                            'Deleted!',
                            'Your entry has been deleted.',
                            'success'
                            )
                          Fire.$emit('AfterCreate');  
                        
                        }).catch( () => {
                            Swal.fire(
                            'Failed!',
                            'something went wrong.',
                            'warning');
                        });

                    }
                })
            }, 
            
            loadUsers() {
              if (this.$gate.isAdmin()) {
                axios.get("api/user").then(({ data }) => (this.users = data));
              } 
            },
            
            createUser() {
            this.$Progress.start();
            this.form.post('api/user')
            .then(() => {
                Fire.$emit('AfterCreate');

                $('#addNew').modal('hide');

                Toast.fire({
                    icon: 'success',
                    title: 'User created successfully'
                });

                this.$Progress.finish();   
            })
            .catch(() => {
                this.$Progress.fail();
            })

            
           } 
        },

        created() {
            // Creating listener for calling our custom searching event;
            Fire.$on('searching',() => {
              // getting data from parent;
              let query = this.$parent.search;
              axios.get('api/findUser?q=' + query)
              .then( (data) => {
                this.users = data.data;
              })
              .catch( () => {
                
              }); 
            }); 

            this.loadUsers();

            Fire.$on('AfterCreate',() => {
                this.loadUsers();
            });
          //  setInterval(() =>this.loadUsers(), 3000);
        }
    }
</script>
