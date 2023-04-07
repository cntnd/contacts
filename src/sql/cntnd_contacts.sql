--
-- Tabellenstruktur für Tabelle `cntnd_contacts`
--

CREATE TABLE `cntnd_contacts` (
  `id` int(11) NOT NULL,
  `idart` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `serializeddata` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `cntnd_contacts`
--
ALTER TABLE `cntnd_contacts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `cntnd_contacts`
--
ALTER TABLE `cntnd_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
