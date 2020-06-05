<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th v-for="column in columns">
                                    <input type="text"
                                           class="form-control"
                                           :value="column.key"
                                           @input="updateColumnKey(column, $event)"
                                    />
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>


<!--                        <button type="button" class="btn btn-secondary"-->
<!--                                @click="$emit('popUpAddColumn',[{showAddColumn: !showAddColumn})"-->
<!--                        >Add Column</button>-->

                        <b-row>
                            <b-col style="display: inline">
                                <AddColumn
                                    :modalShow="showAddColumn"
                                    v-on:captured-column-name="addColumn($event)"
                                ></AddColumn>
                                <button type="button" class="btn btn-secondary">Add Row</button>

                            </b-col>
                        </b-row>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="button" @click="submit()">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import AddColumn from "./AddColumn";
    export default {
        name: "CSVGenerator",

        components: {AddColumn},

        data() {
            return {
                showAddColumn: false,

                data: [
                    {
                        first_name: 'Jon',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },

                ],
                columns: [
                    {key: 'first_name'},
                    {key: 'last_name'},
                    {key: 'emailAddress'},

                ]
            }
        },

        methods: {
            captureColumnName() {

            },

            addRow() {
                // Add new row to data with column keys
            },

            removeRow(row_index) {
                // remove the given row
            },

            addColumn(columnName) {
                if (typeof(columnName) == "undefined") {
                    throw new Exception('Oops! missing column name')
                }

                this.columns.push({key: columnName})
                this.updateDataColumnKey(columnName)
            },

            updateDataColumnKey(columnKey) {
                this.data.forEach(
                    (row) => {
                        if (!row.hasOwnProperty(columnKey)) {
                            row[columnKey] = '';
                        }
                    }
                )
            },

            updateColumnKey(column, event) {
                let oldKey = column.key;

                let columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

                column.key = event.target.value;

                if (columnKeyExists) {
                    column.key = event.target.value.substring(0, event.target.value.length - 1);
                    return;
                }

                this.data.forEach(
                    (row) => {
                        if (row[oldKey]) {
                            row[column.key] = row[oldKey];
                            delete row[oldKey];
                        }
                    }
                )
            },

            submit() {
                return axios.patch('/api/csv-export', this.data);
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
