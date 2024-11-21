import { defineStore } from "pinia";
import { usePatientsStore } from "./patients";
import { useCallsStore } from "./calls";
import { useAppointmentsStore } from "./appointments";
import { useDepositsStore } from "./deposits";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        charts: {},
        fetching: true,
    }),
    actions: {
        async getCharts(params) {
            this.fetching = true;

            const { statusText, data } = await this.axios.get(
                "/dashboard/charts",
                { params }
            );
            this.fetching = false;

            if (statusText === "OK") {
                const patients = usePatientsStore()
                patients.statuses = data.statuses.patient
                
                const calls = useCallsStore()
                calls.statuses = data.statuses.call

                const appointments = useAppointmentsStore()
                appointments.statuses = data.statuses.appointment

                const deposits = useDepositsStore()
                deposits.statuses = data.statuses.deposit

                delete data.statuses

                Object.keys(data).forEach((k) => (this.charts[k] = data[k]));
            }
        },
    },
});
