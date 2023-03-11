<div id="editor" class="overlay">
    <div class="popup">
        <h2>Editor</h2>
        <a class="close" href="#">&times;</a>
        <form>
            <div class="content">
                <fieldset>
                    <legend>Person</legend>
                    <div class="d-flex">
                        <div class="form-group w-50">
                            <label for="vorname">Vorname</label>
                            <input id="vorname" type="text"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="name">Name</label>
                            <input id="name" type="text"/>
                        </div>
                    </div>

                    <div class="form-group highlight">
                        <label for="strasse">Strasse</label>
                        <input id="strasse" type="text"/>
                    </div>

                    <div class="d-flex">
                        <div class="form-group w-25">
                            <label for="plz">PLZ</label>
                            <input id="plz" type="number"/>
                        </div>

                        <div class="form-group w-75">
                            <label for="ort">Ort</label>
                            <input id="ort" type="text"/>
                        </div>
                    </div>

                    <div class="form-group w-50">
                        <label for="land">Land</label>
                        <input id="land" type="text"/>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Kontakte</legend>

                    <div class="d-flex">
                        <div class="form-group w-50">
                            <label for="telefon_geschaeftlich">Telefon geschäftlich</label>
                            <input id="telefon_geschaeftlich" type="tel"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="telefon">Telefon</label>
                            <input id="telefon" type="tel"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="mobile">Mobile</label>
                            <input id="mobile" type="tel"/>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="form-group w-50 highlight">
                            <label for="email">E-Mail</label>
                            <input id="email" type="email"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="email_2">E-Mail 2</label>
                            <input id="email_2" type="email"/>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Newsletter</legend>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input id="check:infomail_spontan" class="form-check-input" type="checkbox" value="x"/>
                            <label for="check:infomail_spontan" class="form-check-label">Infomail Spontan</label>
                        </div>
                        <div class="form-check form-check-inline highlight">
                            <input id="check:newsletter" class="form-check-input" type="checkbox" value="x"/>
                            <label for="check:newsletter" class="form-check-label">Newsletter</label>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Kategorien</legend>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input id="tag:familie" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:familie" class="form-check-label">Familie</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:freunde" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:freunde" class="form-check-label">Freunde</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:kollegen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:kollegen" class="form-check-label">Kollegen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:nachbarn" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:nachbarn" class="form-check-label">Nachbarn</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:wanderleiter" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:wanderleiter" class="form-check-label">Wanderleiter</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:bergsportunternehmen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:bergsportunternehmen" class="form-check-label">Bergsportunternehmen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:geschaeftskollegen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:geschaeftskollegen" class="form-check-label">Geschäftskollegen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:dienstleister" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:dienstleister" class="form-check-label">Dienstleister</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:linkedin" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:linkedin" class="form-check-label">LinkedIn</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:unternehmen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:unternehmen" class="form-check-label">Unternehmen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="tag:organisationen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="tag:organisationen" class="form-check-label">Organisationen</label>
                        </div>
                    </div>

                </fieldset>
            </div>
            <div class="action">
                <button class="btn btn-primary" type="submit">Speichern</button>
                <button class="btn btn-light" type="reset">Zurücksetzen</button>
                <button class="btn btn-dark right" type="button">Löschen</button>
            </div>
        </form>
    </div>
</div>