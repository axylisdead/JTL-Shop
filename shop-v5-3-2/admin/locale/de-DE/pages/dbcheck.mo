��    D      <  a   \      �     �     �     	  
        $     0     8  
   D     O     ]     m     �     �     �     �     �     �          %     <     Q     j     w     �     �     �     �     �     �     �     
       	   .     8     H     T     b     x     �     �     �     �     �     	     	     (	     ?	     Q	  	   h	  	   r	     |	     �	     �	     �	     �	     �	     
      
     1
     A
     W
     m
     �
     �
     �
  	   �
     �
  (   �
               /     I     a     q  b   �     �  '   �  ,   '  <   T  H   �     �     �  $        ;     X     s     �     �  �   �     �  P   �     8  .   Y     �  J   �  #   �  ,        D     [     s     �     �     �  $   �  7   �  +   ,  �   X  �   J  �  �  �   �  �  �  Z   R  ,  �  !  �  �   �  �   �     X  
   d     o  "   �  (   �  #   �      �  �     7   �       n   .  l   �  �   
      �   �   �   c   �!  R   �!  F   @"  X   �"         >      1       ,   "                -                   
   8   D         @                     %   6                 2   ;   4   (       )                                9      7   /   C           &      *   '       A              :                    #      	              .          3      B             =   <       $   5      ?   !   +      0       buttonCreateScript buttonMigrationStart cancelMigration clearCache countTables dbcheck dbcheckDesc dbcheckURL errorDoAction errorEmptyCache errorMigrationTable errorMigrationTableContinue errorMigrationTable_1 errorMigrationTable_128 errorMigrationTable_16 errorMigrationTable_2 errorMigrationTable_32 errorMigrationTable_4 errorMigrationTable_64 errorNoInnoDBSupport errorNoInnoDBSupportDesc errorNoTable errorNoUTF8Support errorNoUTF8SupportDesc errorReadStructureFile errorRowMissing errorTableInUse fullTextDeactivate fullTextDelete ifNecessaryUpTo lessThanOneMinute maintenanceActive migrateOf migrationCancel migrationOf noMaintenance notApproveMaintenance notEnoughTableSpace notEnoughTableSpaceLong noteMigrationScript noteMigrationScriptClick noteMigrationScriptDesc noteMigrationScriptMaintenance notePatienceOne notePatienceTwo noteRecommendMigration noteSoloMigration noteSoloMigrationClick oneMinute rowformat showModifiedTables soloStructureTable startAutomaticMigration structureMigration structureMigrationNeeded structureMigrationNeededLong sureCancelStructureMigration viaScriptConsole warningDoBackup warningDoBackupScript warningDoBackupSingle warningOldDBVersion warningOldDBVersionLong warningUseConsoleScript warningUseThisShopScript yesBackup yesEnoughSpace Content-Type: text/plain; charset=UTF-8
 Skript erstellen Migration starten Migration wird beendet… Cache wird bereinigt… Anzahl Tabellen Datenbankprüfung Mit der Datenbankprüfung können Sie die Konsistenz der Datenbank Ihres Onlineshops überprüfen. https://jtl-url.de/vrf0o Aktion konnte nicht ausgeführt werden. Leeren des Objekt-Cache fehlgeschlagen! (%s) Bei der Migration der Tabelle %s ist ein Fehler aufgetreten! Bei der Migration der Tabelle %s ist ein Fehler aufgetreten! Fortfahren? %s ist keine InnoDB-Tabelle Feldlänge zu kurz in Spalte %s Inkonsistente Kollation in Spalte %s %s hat die falsche Kollation Datentyp text in Spalte %s %s hat das falsche Row-Format Datentyp tinyint in Spalte %s InnoDB wird nicht unterstützt! Ihre aktuelle Datenbankversion %s unterstützt keine InnoDB-Tabellen – eine Struktur-Migration ist nicht möglich.<br/> Bitte setzen Sie sich mit Ihrem Datenbank-Administrator oder Ihrem Hoster zwecks Aktivierung der InnoDB-Unterstützung in Verbindung. Tabelle nicht vorhanden Die UTF-8-Kollation <strong>utf8mb4_unicode_ci</strong> wird nicht unterstützt! Ihre aktuelle Datenbankversion %s unterstützt die Kollation „utf8mb4_unicode_ci“ nicht – eine Struktur-Migration ist nicht möglich.<br/> Bitte setzen Sie sich mit Ihrem Datenbank-Administrator oder Ihrem Hoster zwecks Aktivierung der Kollation „utf8_unicode_ci“ in Verbindung. Struktur-Datei %s konnte nicht gelesen werden. Spalte %s in %s nicht vorhanden  ist in Benutzung und kann nicht migriert werden! Möchten Sie fortfahren? Die Volltextsuche wird deaktiviert. Der Volltextindex %s für %s wird gelöscht. ggfs. aber auch bis zu weniger als eine Minute Wartungsmodus ist aktiv. Migrieren von  Migration abbrechen Migrieren von %s – Schritt %s Ich verzichte auf den Wartungsmodus. Bitte bestätigen Sie den Wartungsmodus und das Backup. Nicht genügend Platz im InnoDB-Tablespace. Im InnoDB-Tablespace Ihrer Datenbank stehen nur %s für Daten zur Verfügung. Dies wird für die zu migrierende Datenmenge u.&nbsp;U. nicht ausreichen. Bitte stellen Sie sicher, dass genügend Platz im InnoDB-Tablespace zur Verfügung steht. Die Migration per Skript über die MySQL-Konsole wird empfohlen, wenn Sie administrativen Zugang zu Ihrem Datenbankserver haben und eine große Menge an Daten migriert werden muss. Mit einem Klick auf die Schaltfläche „Skript erstellen“ können Sie sich ein Skript zur Durchführung der notwendigen Migration generieren lassen. Dieses Skript können Sie dann komplett oder in Teilen auf der Konsole Ihres Datenbankservers ausführen. Sie benötigen dafür einen administrativen Zugang (z.&nbsp;B. per SSH) zu Ihrem Datenbank-Server. Eine Weboberfläche wie phpMyAdmin ist für das Ausführen dieses Skriptes <strong>nicht</strong> geeignet. Das Skript wird anhand der aktuellen Situation erstellt und beinhaltet nur die Änderungen, die für diesen JTL-Shop notwendig sind. Sie können das Skript nicht verwenden, um die Migration auf einem anderen JTL-Shop auszuführen. Bedenken Sie beim Ausführen des Skriptes, dass dieses ggfs. eine längere Zeit für den kompletten Durchlauf benötigt und währenddessen wichtige Tabellen im Onlineshop für den Zugriff gesperrt werden. Es wird deshalb empfohlen, den <a title="Globale Einstellungen - Wartungsmodus" href="%s/config?kSektion=1#wartungsmodus_aktiviert">Wartungsmodus</a> zu aktivieren, während Sie die Migration durchführen. Bitte haben Sie Geduld! Bei %s Tabellen und einer Datenmenge von ca. %s kann die Migration  dauern. Während der Migration werden zudem wichtige Tabellen im Onlineshop gesperrt, so dass es zu erheblichen Einschränkungen im Frontend kommen kann. Es wird deshalb empfohlen, den <a title="Globale Einstellungen - Wartungsmodus" href="%s/config?kSektion=1#wartungsmodus_aktiviert">Wartungsmodus</a> zu aktivieren, während Sie die Migration durchführen.<br/> Jede Tabelle wird einzeln in zwei Schritten migriert. Im ersten Schritt erfolgt die Verschiebung in den InnoDB-Tablespace und im zweiten die Konvertierung der Daten in den UTF-8-Zeichensatz. Die automatische Migration wird empfohlen, wenn die Datenbank Ihres Onlineshops komplett umgestellt werden muss und sich die Datenmenge innerhalb der <a title="Softwarebeschränkungen und Grenzen der JTL-Produkte" href="https://jtl-url.de/8qsat">Spezifikationen</a> für JTL-Shop befindet. Die Einzel-Migration wird empfohlen, wenn nur einige wenige Tabellen geändert werden müssen oder einzelne Tabellen mit der automatischen Migration oder der Migration per Skript nicht geändert werden konnten. Sie können mit einem Klick auf das <i class="fa fa-cogs"></i>-Symbol die Migration für jede Tabelle einzeln in der Liste durchführen. eine Minute Row-Format Anzahl modifizierter Tabellen Einzeln über die Struktur-Tabelle Automatische Migration wird gestartet… Struktur-Migration für %s Tabellen Struktur-Migration erforderlich! Für %s Tabellen ist eine Verschiebung in den InnoDB-Tablespace und ggfs. die Konvertierung in einen UTF-8-Zeichensatz erforderlich. Von dieser Migration sind ca. %s an Daten betroffen. Möchten Sie die Struktur-Migration wirklich abbrechen? Per Skript auf der DB-Konsole Erstellen Sie unbedingt ein Backup der gesamten Datenbank <strong>BEVOR</strong> Sie die Migration ausführen! Erstellen Sie unbedingt ein Backup der gesamten Datenbank, <strong>BEVOR</strong> Sie das Skript ausführen. <strong>BEVOR</strong> Sie die Migration durchführen, erstellen Sie unbedingt ein Backup der gesamten Datenbank, mindestens jedoch der Tabellen, die Sie ändern möchten. Veraltete Datenbank-Version Die verwendete Datenbank-Version %s unterstützt nicht alle Möglichkeiten dieser Version von JTL-Shop. Einige Funktionen stehen deshalb nach der Migration nicht mehr zur Verfügung. Verwenden Sie eine Serverkonsole und <strong>NICHT</strong> phpMyAdmin zum Ausführen des Skriptes. Verwenden Sie das Skript nur für die Migration <strong>DIESES</strong> JTL-Shops. Ich habe ein Backup der kompletten Datenbank des Onlineshops erstellt. Ich habe sichergestellt, dass genügend Platz im InnoDB-Tablespace zur Verfügung steht. 