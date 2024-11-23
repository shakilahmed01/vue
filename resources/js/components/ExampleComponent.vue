<template>
    <div>
        <!-- Application Header -->
        <header class="bg-primary text-white p-3 mb-4">
            <h1 class="text-center">Employee Management System using Laravel and Vue Js</h1>
        </header>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mt-5">
                        <div class="card-header">Add Employee</div>
                        <div class="card-body">
                            <form @submit.prevent="submitForm">
                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" v-model="form.name" class="form-control" />

                                    <label class="form-label">Phone</label>
                                    <input type="text" v-model="form.phone" class="form-control" />

                                    <label class="form-label">Address</label>
                                    <input type="text" v-model="form.address" class="form-control" />

                                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Search and Employee List -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>List Employees</span>
                                <input type="text" v-model="searchQuery" class="form-control w-50"
                                    placeholder="Search by name, phone, or address" />
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="employee in filteredEmployees" :key="employee.id">
                                        <td>{{ employee.name }}</td>
                                        <td>{{ employee.phone }}</td>
                                        <td>{{ employee.address }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm m-1" @click="editEmployee(employee)">
                                                Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm m-1"
                                                @click="deleteEmployee(employee.id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Pagination Controls -->
                            <nav v-if="pagination" aria-label="Page navigation example">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
                                        <button class="page-link" @click="fetchEmployees(pagination.current_page - 1)"
                                            :disabled="!pagination.prev_page_url">
                                            Previous
                                        </button>
                                    </li>
                                    <li class="page-item" v-for="page in totalPages" :key="page"
                                        :class="{ active: page === pagination.current_page }">
                                        <button class="page-link" @click="fetchEmployees(page)">{{ page }}</button>
                                    </li>
                                    <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
                                        <button class="page-link" @click="fetchEmployees(pagination.current_page + 1)"
                                            :disabled="!pagination.next_page_url">
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>



<script>
export default {
    data() {
        return {
            form: {
                name: '',
                phone: '',
                address: ''
            },
            employees: [],
            pagination: null, // Store pagination data here
            searchQuery: '', // Track search input
            isEditing: false,
            currentEmployeeId: null
        };
    },
    mounted() {
        this.fetchEmployees(); // Fetch the first page on mount
    },
    computed: {
        totalPages() {
            return this.pagination ? Array.from({ length: this.pagination.last_page }, (_, i) => i + 1) : [];
        },
        filteredEmployees() {
            if (!this.searchQuery) {
                return this.employees;
            }
            return this.employees.filter(employee => {
                const query = this.searchQuery.toLowerCase();
                return (
                    employee.name.toLowerCase().includes(query) ||
                    employee.phone.toLowerCase().includes(query) ||
                    employee.address.toLowerCase().includes(query)
                );
            });
        }
    },
    methods: {
        async fetchEmployees(page = 1) {
            try {
                const response = await axios.get(`/api/employees?page=${page}`);
                this.employees = response.data.data;
                this.pagination = response.data; // Store pagination info
            } catch (error) {
                console.error('Error fetching employees:', error);
            }
        },
        async submitForm() {
            if (this.isEditing) {
                await this.updateEmployee();
            } else {
                await this.addEmployee();
            }
        },
        async addEmployee() {
            try {
                await axios.post('/api/employees', this.form);
                alert('Employee added successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh the current page
                this.resetForm();
            } catch (error) {
                console.error('Error adding employee:', error);
                alert('Failed to add employee!');
            }
        },
        async updateEmployee() {
            try {
                await axios.put(`/api/employees/${this.currentEmployeeId}`, this.form);
                alert('Employee updated successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh the current page
                this.resetForm();
                this.isEditing = false; // Exit editing mode
            } catch (error) {
                console.error('Error updating employee:', error);
                alert('Failed to update employee!');
            }
        },
        editEmployee(employee) {
            this.isEditing = true;
            this.currentEmployeeId = employee.id;
            this.form = { ...employee }; // Populate the form with employee's data
        },
        async deleteEmployee(id) {
            if (!confirm('Are you sure you want to delete this employee?')) {
                return;
            }
            try {
                await axios.delete(`/api/employees/${id}`);
                alert('Employee deleted successfully!');
                this.fetchEmployees(this.pagination.current_page); // Refresh the current page
            } catch (error) {
                console.error('Error deleting employee:', error);
                alert('Failed to delete employee!');
            }
        },
        resetForm() {
            this.form = { name: '', phone: '', address: '' };
            this.currentEmployeeId = null;
            this.isEditing = false;
        }
    }
};
</script>
