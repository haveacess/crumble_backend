<form method="POST" action="{{ $action }}">
    <label>
        Imported account
        <input id="accountName" type="text" name="account_name" value="{{ $accountName }}" disabled>
    </label>

    <br><br>

    <label>
        Select files on your PC
        <input type="file" name="cookie_file">
    </label>
    <br>
    <textarea
        name="cookie"
        rows="24" cols="80"
        placeholder="Or put Cookie's in Json format here"></textarea>
    <br>

    <button type="submit">Import Cookie's</button>
</form>
