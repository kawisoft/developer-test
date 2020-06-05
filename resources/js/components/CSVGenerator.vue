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
                                <th>
                                    <h4>Action</h4>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, idx) in data" :key="idx">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td>
                                    <b-button variant="danger" @click="confirmRemoval(idx)">Delete</b-button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-right">
                        <b-button-toolbar aria-label="Toolbar with button groups and dropdown menu">
                            <AddColumn style="margin-right: 20px"
                                :modalShow="showAddColumn"
                                v-on:captured-column-name="addColumn($event)"
                            ></AddColumn>
                            <button type="button" class="margin-left btn btn-secondary" @click="addRow()">Add Row</button>
                            <b-col>
                                <button ref="submit" class="btn btn-primary" type="button" @click="submit()">Export</button>
                            </b-col>
                        </b-button-toolbar>

                    </div>
                </div>

                <b-overlay :show="busy" no-wrap @shown="onShown" @hidden="onHidden">
                    <template v-slot:overlay>
                        <div ref="dialog" v-if="processing" class="text-center p-4 bg-primary text-light rounded">
                            <b-icon icon="cloud-upload" font-scale="4"></b-icon>
                            <div class="mb-3">Processing...</div>
                            <b-progress
                                min="1"
                                max="20"
                                :value="counter"
                                variant="success"
                                height="3px"
                                class="mx-n4 rounded-0"
                            ></b-progress>
                        </div>
                        <div
                            v-else
                            ref="dialog"
                            tabindex="-1"
                            role="dialog"
                            aria-modal="false"
                            aria-labelledby="form-confirm-label"
                            class="text-center p-3"
                        >
                            <p><strong id="form-confirm-label">Are you sure?</strong></p>
                            <div class="d-flex">
                                <b-button variant="outline-danger" class="mr-3" @click="onCancel">
                                    Cancel
                                </b-button>
                                <b-button variant="outline-success" @click="removeRow()">OK</b-button>
                            </div>
                        </div>
                    </template>
                    </b-overlay>
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
                busy: false,
                processing: false,
                counter: 1,
                interval: null,
                selectedRowIndex: null,

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

        beforeDestroy() {
            this.clearInterval()
        },

        methods: {
            addRow() {
                const newRow = {};
                this.columns.forEach(
                    item => {
                        newRow[item.key] = ''
                    }
                )

                this.data.push(newRow);
            },

            confirmRemoval(rowIndex) {
                this.processing = false
                this.busy = true;
                this.selectedRowIndex = rowIndex;
                console.log(this.selectedRowIndex);
            },

            removeRow() {
                console.log(this.selectedRowIndex);

                this.data.splice(this.selectedRowIndex, 1);
                this.processing = true;
                this.busy = false;
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

            async submit() {
                this.processing = true;
                this.busy = true;
                let header = [];

                this.columns.map(col => {
                    header.push(col.key);
                });

                try {
                    const res = await axios.post('/api/csv-export', {
                        header: header,
                        payload: this.data,
                        responseType: 'blob'
                    });

                    this.busy = this.processing = false;
                    this.processDownload(res.data);

                } catch (err) {
                    alert(`Oops! request failed`);
                    this.busy = this.processing = false;
                }
            },

            processDownload(data) {
                const url = window.URL.createObjectURL(new Blob([data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'f3group.csv');
                document.body.appendChild(link);
                link.click();
            },

            onShown() {
               this.$refs.dialog.focus()
            },
            onHidden() {
                this.$refs.submit.focus()
            },
            onCancel() {
                this.busy = false
            },
        }
    }
</script>

<style scoped>

</style>
