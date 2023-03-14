<div id="editor" class="overlay">
    <div class="popup">
        <h2>Editor</h2>
        <span class="close" href="#">&times;</span>
        <form name="editor_form" id="editor_form" method="post">
            <div class="content">
                <fieldset>
                    <legend>Person</legend>
                    <div class="d-flex">
                        <div class="form-group w-50">
                            <label for="vorname">Vorname*</label>
                            <input id="vorname" name="vorname" type="text" required/>
                        </div>

                        <div class="form-group w-50">
                            <label for="name">Name*</label>
                            <input id="name" name="name" type="text" required/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="strasse">Strasse</label>
                        <input id="strasse" name="strasse" type="text"/>
                    </div>

                    <div class="d-flex">
                        <div class="form-group w-25">
                            <label for="plz">PLZ</label>
                            <input id="plz" name="plz" type="number"/>
                        </div>

                        <div class="form-group w-75">
                            <label for="ort">Ort</label>
                            <input id="ort" name="ort" type="text"/>
                        </div>
                    </div>

                    <div class="form-group w-50">
                        <label for="land">Land</label>
                        <input id="land" name="land" type="text"/>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Kontakte</legend>

                    <div class="d-flex">
                        <div class="form-group w-50">
                            <label for="telefon_geschaeftlich">Telefon geschäftlich</label>
                            <input id="telefon_geschaeftlich" name="telefon_geschaeftlich" type="tel"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="telefon">Telefon</label>
                            <input id="telefon" name="telefon" type="tel"/>
                        </div>

                        <div class="form-group w-50">
                            <label for="mobile">Mobile</label>
                            <input id="mobile" name="mobile" type="tel"/>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="form-group w-50">
                            <label for="email">E-Mail*</label>
                            <input id="email" name="email" type="email" required/>
                        </div>

                        <div class="form-group w-50">
                            <label for="email_2">E-Mail 2</label>
                            <input id="email_2" name="email_2" type="email"/>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Newsletter</legend>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input id="infomail_spontan" name="infomail_spontan" class="form-check-input"
                                   type="checkbox" value="x"/>
                            <label for="infomail_spontan" class="form-check-label">Infomail Spontan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="newsletter" name="newsletter" class="form-check-input" type="checkbox"
                                   value="x"/>
                            <label for="newsletter" class="form-check-label">Newsletter</label>
                        </div>
                    </div>

                </fieldset>

                <fieldset>
                    <legend>Kategorien</legend>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input id="familie" name="familie" class="form-check-input" type="checkbox" value="x"/>
                            <label for="familie" class="form-check-label">Familie</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="freunde" name="freunde" class="form-check-input" type="checkbox" value="x"/>
                            <label for="freunde" class="form-check-label">Freunde</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="kollegen" name="kollegen" class="form-check-input" type="checkbox" value="x"/>
                            <label for="kollegen" class="form-check-label">Kollegen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="nachbarn" name="nachbarn" class="form-check-input" type="checkbox" value="x"/>
                            <label for="nachbarn" class="form-check-label">Nachbarn</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="wanderleiter" name="wanderleiter" class="form-check-input" type="checkbox"
                                   value="x"/>
                            <label for="wanderleiter" class="form-check-label">Wanderleiter</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="bergsportunternehmen" name="bergsportunternehmen" class="form-check-input"
                                   type="checkbox" value="x"/>
                            <label for="bergsportunternehmen" class="form-check-label">Bergsportunternehmen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="geschaeftskollegen" name="geschaeftskollegen" class="form-check-input"
                                   type="checkbox" value="x"/>
                            <label for="geschaeftskollegen" class="form-check-label">Geschäftskollegen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="dienstleister" name="dienstleister" class="form-check-input" type="checkbox"
                                   value="x"/>
                            <label for="dienstleister" class="form-check-label">Dienstleister</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="linkedin" name="linkedin" class="form-check-input" type="checkbox" value="x"/>
                            <label for="linkedin" class="form-check-label">LinkedIn</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="unternehmen" name="unternehmen" class="form-check-input" type="checkbox"
                                   value="x"/>
                            <label for="unternehmen" class="form-check-label">Unternehmen</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input id="organisationen" name="organisationen" class="form-check-input" type="checkbox"
                                   value="x"/>
                            <label for="organisationen" class="form-check-label">Organisationen</label>
                        </div>
                    </div>

                </fieldset>
            </div>
            <div class="action">
                <input type="hidden" name="editor_form_action" value="new"/>
                <input type="hidden" name="editor_form_source"/>
                <input type="hidden" name="editor_form_index"/>
                <button class="btn btn-primary" type="submit">Speichern</button>
                <button class="btn btn-light" type="reset">Zurücksetzen</button>
                <button class="btn btn-dark right editor_form_remove" type="button">Löschen</button>
            </div>
        </form>
    </div>
</div>
<div id="delete" style="visibility: hidden;">
    <form name="delete_form" id="delete_form" method="post">
        <input type="hidden" name="editor_form_action" value="delete"/>
        <input type="hidden" name="editor_form_source"/>
        <input type="hidden" name="editor_form_index"/>
    </form>
</div>