import { defineStore } from 'pinia';
import { v4 as uuidv4 } from 'uuid';

type Payload = Record<string, string|null>;

interface Popup {
    id: string;
    title: string;
    body: string;
    icon: string|null;
    confirmButton: string;
    cancelButton: string;
    resolve: (id: string) => void;
    reject: (id: string) => void;
    fields: SwalField[];
    payload: Payload;
}

interface SwalField {
    name: string;
    label: string;
    required: boolean;
    type: 'select' | 'text';
    options: [],
}

export default defineStore('swal', {
    state: () => ({
        popups: [] as Popup[],
    }),
    actions: {
        confirm(title: string, body: string): Promise<void> {
            return new Promise((resolve, reject) =>  {
                new Promise<string>((resolve, reject) => {
                    this.popups.push({
                        title,
                        body,
                        confirmButton: 'Okay',
                        cancelButton: 'Abbrechen',
                        resolve,
                        reject,
                        id: uuidv4(),
                        icon: 'warning-triangle-light',
                        fields: [],
                        payload: {},
                    });
                }).then((id) => {
                    this.remove(id);
                    resolve();
                }).catch((id) => {
                    this.remove(id);
                    reject();
                });
            });
        },

        ask(title: string, body: string, fields: SwalField[] = []): Promise<Payload> {
            return new Promise<Payload>((resolve, reject) =>  {
                new Promise<string>((resolve, reject) => {
                    const payload: Payload = {};
                    fields.forEach(f => payload[f.name] = null);
                    this.popups.push({
                        title,
                        body,
                        confirmButton: 'Okay',
                        cancelButton: 'Abbrechen',
                        resolve,
                        reject,
                        id: uuidv4(),
                        icon: 'warning-triangle-light',
                        fields: fields,
                        payload: payload,
                    });
                }).then((id) => {
                    const p = this.find(id)?.payload;
                    this.remove(id);
                    resolve(p || {});
                }).catch((id) => {
                    this.remove(id);
                    reject();
                });
            });
        },

        remove(id: string) {
            this.popups = this.popups.filter(p => p.id !== id);
        },

        find(id: string): Popup|undefined {
            return this.popups.find(p => p.id === id);
        }
    },
});
