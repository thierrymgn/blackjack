export default class SecurityStore {
    private token: string|null = null;

    public getToken(): string|null {
        return this.token;
    }

    public setToken(token: string): SecurityStore {
        this.token = token;
        return this;
    }

    public clearToken(): SecurityStore {
        this.token = null;
        return this;
    }
}