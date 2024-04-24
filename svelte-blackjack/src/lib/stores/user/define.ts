export default class UserStore {
    public username: string;
    public email: string;
    public wallet: number;

    constructor(username: string, email: string, wallet: number) {
        this.username = username;
        this.email = email;
        this.wallet = wallet;
    }
}