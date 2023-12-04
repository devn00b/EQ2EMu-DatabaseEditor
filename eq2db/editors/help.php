<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EQ2DB Editor: Help</title>
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style2 {
	font-size: 14px;
	font-weight: bold;
}
.style3 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 18px;
	font-weight: bold;
}
-->
</style>
</head>
<body>
<p class="style1">Help Information:</p>
<p class="style1">This is our help file. Nice, yeah? </p>
<a name="luafunctions"></a>
<p><span class="style3">EQ2Emu LUA Functions</span><br />
  <span class="style1">A complete list of LUA functions</span></p>
<p class="style1">//Sets <br />
  SetCurrentHP(Spawn, value) <br />
  SetMaxHP(Spawn, value) <br />
  SetCurrentPower(Spawn, value) <br />
  SetMaxPower(Spawn, value) <br />
  SetHeading(Spawn, value) <br />
  SetRaceType(Spawn, value) <br />
  SetSpeed(Spawn,  value) <br />
  SetPosition(Spawn, x, y, z) <br />
  SetInt(Spawn, value) <br />
  SetWis(Spawn, value) <br />
  SetSta(Spawn, value) <br />
  SetStr(Spawn, value) <br />
  SetAgi(Spawn, value) <br />
  SetLootCoin(Spawn, amount) <br />
  <br />
  //Gets <br />
  GetCurrentHP(Spawn) <br />
  GetMaxHP(Spawn) <br />
  GetCurrentPower(Spawn) <br />
  GetName(Spawn) <br />
  GetMaxPower(Spawn) <br />
  GetDistance(Spawn) <br />
  GetX(Spawn) <br />
  GetY(Spawn) <br />
  GetZ(Spawn) <br />
  GetHeading(Spawn) <br />
  GetRaceType(Spawn) <br />
  GetSpeed(Spawn) <br />
  HasMoved(Spawn) <br />
  GetInt(Spawn) <br />
  GetWis(Spawn) <br />
  GetSta(Spawn) <br />
  GetStr(Spawn) <br />
  GetAgi(Spawn) <br />
  GetLootCoin(Spawn) <br />
  GetZone(ZoneId) <br />
  <br />
  //Misc <br />
  ModifyPower(Spawn, value) <br />
  ModifyHP(Spawn, value) <br />
  SpellDamage(Target, type, damage) <br />
  FaceTarget(Spawn, Target) <br />
  MoveToLocation(Spawn, x, y, z) - should be  used to make an NPC run to a given spot <br />
  Say(Spawn, message) <br />
  Shout(Spawn,  message) <br />
  SayOOC(Spawn, message) <br />
  Emote(Spawn, message) <br />
  IsPlayer(Spawn) <br />
  MovementLoopAddLocation(Spawn, x, y, z, speed, delay,  function) - used to add movements to a spawn <br />
  &nbsp; &nbsp;function: this is a LUA  function defined in the spawnscript that is called once the NPC reaches the  x,y,z specified <br />
  GetCurrentZoneSafeLocation(Spawn) <br />
  PlayFlavor(Spawn,  mp3_filename, text, emote, mp3_key1, mp3_key2) - this allows you to play a voice  file, display chat, and do an emote all at once <br />
  PlaySound(Spawn,  sound_filename, x, y, z) <br />
  PlayVoice(Spawn, mp3_filename, mp3_key1, mp3_key2) <br />
  AddLootItem(spawn, item_id) <br />
  RemoveLootItem(spawn, item_id) <br />
  AddLootCoin(spawn, amount) <br />
  CreateConversation(Spawn, value) - Used to  create a new Conversation.&nbsp;This is necessary before you can&nbsp;set them <br />
  AddConversationOption(Conversation, Text option, Function) - The given  function is called when the user selects this text option. <br />
  StartConversation(Conversation, NPC, Spawn, Text) - This initiates the  conversation and displays what the NPC says (text) and the options that were  added using the AddConversationOption function. <br />
  SummonItem(Spawn, ItemId) <br />
  Spawn(Zone, SpawnID, restricted, x, y, z, heading) <br />
  &nbsp; &nbsp;Zone: Z reference  to the zone.&nbsp; use GetZone(Spawn) to get this. <br />
  &nbsp; &nbsp;restricted: A boolean to  determine if this spawn can only be seen by the player (usually quest related). <br />
  Zone(Zone, Spawn) <br />
  &nbsp; &nbsp;Zone: A reference to the zone to zone the spawn  into.&nbsp; Use GetZone(Spawn) to get this. <br />
  SetPlayerProximityFunction(NPC,  distance, in range function name, out of range function name (optional) ) <br />
  This will call the 'in range function' name when the player's distance to  the NPC is less than the distance given in the function.&nbsp; Also if the 'out of  range function' name is given, that is called when the player leaves the range. <br />
  GetSpawn(Spawn, spawn_id) - Returns the closest Spawn to the Spawn argument  that matches the spawn_id. The value returned as well as the first argument can  be a NPC, Widget, Sign, Object and in the case of the argument even a Player. <br />
  GetZoneID(Zone) - Returns the zone id for the zone. Pass in a Zone object  retrieved with the GetZone function. <br />
  GetZoneName(Zone) - Returns the zone  name for the zone. Pass in a Zone object retrieved with the GetZone function. <br />
  GetNPC(Spawn, spawn id) - returns the closest NPC of that spawn id to the  given Spawn. <br />
  <br />
  //Quest Stuff <br />
  RegisterQuest(Quest, Quest Name, Quest  Type, Quest Zone, Quest level, Description) - REQUIRED <br />
  OfferQuest(NPC,  Spawn, Quest ID) - Offers the quest to the Spawn (Player) <br />
  SetQuestPrereqLevel(Quest, Level) <br />
  AddQuestPrereqQuest(Quest, Quest ID) <br />
  AddQuestPrereqItem(Quest, Item ID, Quantity) <br />
  AddQuestPrereqFaction(Quest, Faction ID, Faction Amount Lower, Faction  Amount Upper) <br />
  AddQuestPrereqRace(Quest, race id) <br />
  AddQuestPrereqClass(Quest, class id) <br />
  AddQuestPrereqTradeskillLevel (not  used yet) <br />
  AddQuestPrereqTradeskillClass (not used yet) <br />
  AddQuestRewardItem(Quest, Item ID, Quantity) <br />
  AddQuestSelectableRewardItem(Quest, item id, quantity) <br />
  AddQuestRewardCoin(Quest, Copper, Silver, Gold, Plat) <br />
  AddQuestRewardFaction(Quest, Faction ID, Amount) <br />
  SetQuestRewardStatus(Quest, Amount) <br />
  SetQuestRewardComment(Quest, Text) <br />
  SetQuestRewardExp(Quest, Amount) <br />
  AddQuestStepKill(Quest, Step ID,  Description, Quantity, Percentage,TaskGroupText, NPC ID(s)) <br />
  &nbsp; &nbsp;Percentage: A  number from 0 to 100 specifying the percent chance to get the update per kill. <br />
  AddQuestStepChat(Quest, Step ID, Description, TaskGroupText, NPC ID(s)) <br />
  AddQuestStepObtainItem(Quest, Step ID, Description, Quantity, Percentage,  TaskGroupText, Item ID(s)) <br />
  &nbsp; &nbsp;Percentage: A number from 0 to 100 specifying  the percent chance to get the update. <br />
  AddQuestStepLocation(Quest, Step ID,  Description, X, Y, Z, MaxVariation, TaskGroupText) <br />
  AddQuestStepCompleteAction(Quest, Step ID, Function Name) <br />
  SetQuestCompleteAction(Quest, Function Name) - Sets the LUA function name  that should be called once the player completes all steps. <br />
  GiveQuestReward(Quest, Player) <br />
  SetStepComplete(Spawn, QuestId, StepId) <br />
  GetQuestStep(Spawn, QuestId) - returns the first non-completed quest step <br />
  UpdateQuestStepDescription(Quest, Step ID, Description) <br />
  GetTaskGroupStep(Spawn, QuestId) <br />
  UpdateQuestTaskGroupDescription(Quest,  TaskGroupId, Text, DisplayBullets) <br />
  &nbsp; &nbsp;DisplayBullets: a boolean whether or  not to display the quest steps assigned to this taskgroup. Defaults to false <br />
  UpdateQuestDescription() <br />
  SetCompletedDescription(Quest, Description) <br />
  ProvidesQuest() <br />
  HasQuest() <br />
  HasCompletedQuest() <br />
  QuestIsComplete(Spawn, Quest ID) - returns true if the player has the quest,  has not turned it in, and it is completed <br />
  QuestReturnNPC(Quest, Spawn ID) -  Sets the spawn id of the spawn that the player needs to turn the quest into. <br />
  GetQuest(Spawn, Quest ID) - Retrieves the existing active quest for the  player. Returns 0 if the player doesn't have the current quest or it is  completed. <br />
  AddTimer() <br />
  <br />
  //Quest Functions <br />
  function Accepted(Quest,  QuestGiver, Player) <br />
  NOTE: This function is called when a player accepts the  quest <br />
  <br />
  function Declined(Quest, QuestGiver, Player) <br />
  NOTE: This  function is called when a player declines the quest <br />
  <br />
  //Spawn Script  Functions <br />
  function InRange(NPC, Player) - called every 2 seconds <br />
  function LeavingRange(NPC, Player) - called every 2 seconds</p>
<p class="style1">&nbsp;</p>
<a name="questfunctions"></a>
<p><span class="style3">Quest Script Functions List<br />
  </span><span class="style1"> The following LUA functions are used in creating/using Quests:<br />
  Note that the first parameter is ALWAYS Quest.  See KillCrabs.lua for a working example of these functions.</span></p>
<p><span class="style2">REQUIRED FUNCTIONS</span> (placed in an init() function):</p>
<p class="style1">RegisterQuest(Quest, Quest Name, Quest Type, Quest Zone, Quest level, Description)</p>
<p class="style1"><strong class="style2">Optional Functions</strong> (mainly used in init() function):</p>
<p class="style1">SetQuestPrereqLevel(Quest, Level) <br />
  NOTE: Level required to be given the quest<br />
  <br />
  AddQuestPrereqQuest(Quest, Quest ID) <br />
  NOTE: Quest that must be completed before this quest can be given</p>
<p class="style1">AddQuestPrereqItem(Quest, Item ID, Quantity)<br />
  NOTE: Quantity is optional and defaults to 1<br />
  Item that the player must have to get the quest</p>
<p class="style1">AddQuestPrereqFaction(Quest, Faction ID, Faction Amount Lower, Faction Amount Upper) <br />
  NOTE: not currently used but here for when it is completed<br />
  Quest is offered when the character's faction is between the Faction Amount Lower and Faction Amount Upper (if Faction Amount Upper is used, otherwise faction is &gt;= Faction Amount Lower)</p>
<p class="style1">AddQuestRewardItem(Quest, Item ID, Quantity)<br />
  NOTE: Quantity is optional and defaults to 1<br />
  <br />
  AddQuestRewardCoin(Quest, Copper, Silver, Gold, Plat) <br />
  NOTE: all parameters but copper are optional</p>
<p class="style1">AddQuestRewardFaction(Quest, Faction ID, Amount) <br />
  NOTE: not currently used but here for when it is completed</p>
<p class="style1">SetQuestRewardStatus(Quest, Amount) <br />
  NOTE: not currently used but here for when it is completed</p>
<p class="style1">SetQuestRewardComment(Quest, Text)</p>
<p class="style1">SetQuestRewardExp(Quest, Amount)</p>
<p class="style1">AddQuestStepKill(Quest, Step ID, Description, Quantity, TaskGroupText, NPC ID(s)) <br />
  NOTE: ID is a unique number that you want to use for this step.  It is used later to track player progress. Each quest can use whichever IDs you want as long as the ID is not repeated in the same quest.<br />
  TaskGroupText is optional, but if used this Quest Step will be a bullet underneath the TaskGroup created with the TaskGroupText. <br />
  If you dont want to use TaskGroupText use an empty string in the field (ie &quot;&quot;)<br />
  You can have multiple bullets under the same TaskGroup by using the same TaskGroupText in multiple addQuestStepKill function calls.</p>
<p class="style1">AddQuestStepChat(Quest, Step ID, Description, TaskGroupText, NPC ID(s)) <br />
  <br />
  AddQuestStepObtainItem(Quest, Step ID, Description, Quantity, TaskGroupText, Item ID(s))</p>
<p class="style1">AddQuestStepLocation(Quest, Step ID, Description, X, Y, Z, MaxVariation, TaskGroupText)<br />
  NOTE: MaxVariation is the distance the player can be from the location and still get credit</p>
<p class="style1">AddQuestStepCompleteAction(Quest, Step ID, Function Name)<br />
  NOTE: LUA Function that is called when this step is completed.</p>
<p class="style1">SetCompletedDescription(Quest, Description)<br />
  NOTE: Description that is used after a quest has been completed.</p>
<p class="style1">UpdateQuestStepDescription(Quest, Step ID, Description)</p>
<p class="style1">function Accepted(Quest, QuestGiver, Player)<br />
  NOTE: This function is called when a player accepts the quest</p>
<p class="style1">function Declined(Quest, QuestGiver, Player)<br />
  NOTE: This function is called when a player declines the quest</p>
<p class="style1">&nbsp; </p>
<a name="spellfunctions"></a>
<p><span class="style3">Spells LUA Functions List</span><br />
  <span class="style1">Can't seem to find these anymore :D</span> </p>
<a name="spellfields"></a>
<p><span class="style3">Table: `spells` Field List</span> <span class="style1">(by chrrox)</span> </p>
<p><span class="style1">ID: <br />
  This is the unique ID for your spell. Just   choose a number you have not used yet. <br />
  <br />
  Type: <br />
  Unknown <br />
  <br />
  Cast_Type: <br />
  Normal<br />
  Toggle</span><span class="style1"> <br />
  <br />
  Name: <br />
  The   Name you give your spell. <br />
  Example : Sprint <br />
  <br />
  Description: <br />
  Gives the player a short description of the spell when the put thier mouse over it appears in the   upper right corner under spell name. <br />
  <br />
  Icon: <br />
  This is the top icon and is the icon that will be shown in your spell book. I will update the id's as i try them. <br />
  1: Blue lightning bolt <br />
  Icons 792 - 2001 are just plain white. <br />
  <br />
  Icon2: <br />
  This is the   bottom icon of your spell this will show the icon   that is used to make combo's. I will update the id's as i try them. <br />
  1:   Warrior type / blue / shield / 1 <br />
  2: Warrior type / blue / horn / 1 <br />
  3:   Warrior type / blue / fist / 1 <br />
  4: Warrior type / blue / boot / 1 <br />
  5:   Warrior type / blue / muscle-flex / 1 <br />
  6: Warrior type / blue / sword / 1 <br />
  7: Warrior type / blue / shield / 2 <br />
  8: Warrior type / blue / horn / 2 <br />
  9: Warrior type / blue / fist / 2 <br />
  10: Warrior type / blue / boot / 2 <br />
  11: Warrior type / blue / muscle-flex / 2 <br />
  12: Healer Type / gold /   chalice / 1 <br />
  13: Healer Type / gold / symbol / 1 <br />
  14: Healer Type / gold /   hammer / 1 <br />
  15: Healer Type / gold / eye / 1 <br />
  16: Healer Type / gold /   moon / 1 <br />
  17: Healer Type / gold / stone henge / 1 <br />
  18: Healer Type / gold   / chalice / 2 <br />
  19: Healer Type / gold / symbol / 2 <br />
  20: Healer Type / gold   / hammer / 2 <br />
  21: <br />
  22: <br />
  23: <br />
  24: <br />
  25: <br />
  26: <br />
  27: <br />
  28: <br />
  29: <br />
  30: <br />
  31: <br />
  32: <br />
  33: <br />
  34: <br />
  35: <br />
  36: <br />
  37: <br />
  38: <br />
  39: <br />
  40: <br />
  41: <br />
  42: <br />
  43: <br />
  44: <br />
  45: <br />
  46: <br />
  47: <br />
  48:   Generic icon. <br />
  <br />
  The max number here is 48 before the picture is just   blank. </span></p>
<p class="style1">Icontype: <br />
  This is the background color of the spell. This replaces the lightning bolt background.   Valid choices are. <br />
  312-green <br />
  313-blue <br />
  314-purple <br />
  315-red <br />
  316-orange <br />
  317-yellow <br />
  <br />
  Class_Skill: <br />
  I am unsure of what this   effects. I leave it at 0 <br />
  <br />
  Mastery_Skill: <br />
  I am unsure of what this   effects. I leave it at 0 <br />
  <br />
  Tier: <br />
  This is what determines what level   your spell is. <br />
  Here are the values. <br />
  1:   Apprentice 1 <br />
  2: Apprentice 2 <br />
  3: Apprentice 3 <br />
  4: Apprentice 4 <br />
  5:   Adept 1 <br />
  6: Adept 2 <br />
  7: Adept 3 <br />
  8: Adept 4 <br />
  9: Master 1 <br />
  10:   Master 2 <br />
  11: Master 3 <br />
  12: Master 4 <br />
  <br />
  HP_Req: <br />
  Required HP in   order to attempt to cast the spell. <br />
  <br />
  Power_Req: <br />
  Required Power in order to attempt to cast the spell. <br />
  <br />
  Power_Upkeep: <br />
  How much it costs to   keep the spell going. <br />
  example I cast sprint   for 8 power and I set my cast time to 6 I would have to pay 8 power every 6   seconds <br />
  to keep the buff going. </p>
<p><span class="style1"> Cast_Time: <br />
  How long before you must   pay the spells upkeep (time in seconds) <br />
  <br />
  Recast: <br />
  How long before you   can recast your spell after it ends. <br />
  <br />
  Radius: <br />
  How far in meters away from the current target the spell will effect. <br />
  <br />
  Max_AOE_targets: <br />
  when   the radius is greater than 0 the max number of targets the spell will effect. <br />
  <br />
  Req_Concentration: <br />
  How   much concentration you need in order to cast the spell. (I set to 0 for now) <br />
  <br />
  Range: <br />
  How   far away the rarget can be from the caster in meters. <br />
  <br />
  Duration1: <br />
  Minimum length of the spell duration. <br />
  <br />
  Duration2: <br />
  Maximum length of the spell duration. <br />
  <br />
  Duration_Until_Cancel: <br />
  If the first 2 are not specified   this says it will last forever until canceled. <br />
  <br />
  Call_Frequency: <br />
  Unknown ( I set to 0) <br />
  <br />
  Resistibility: <br />
  Chance of target resisting   the spell. <br />
  <br />
  Target_Type: <br />
  What object   you are allowed to cast the spell on. <br />
  0: Self <br />
  1: Enemy <br />
  2: Unknown / any? <br />
  3: Passive spell / ability <br />
  4: Enemy's Pet <br />
  5: Enemy's   Corpse <br />
  6: Group Members Corpse <br />
  7: Unknown <br />
  8: Raid (AE) <br />
  9:   Unknown <br />
  10: Unknown <br />
  11: Unknown <br />
  12: Unknown <br />
  13: Unknown <br />
  14:   Unknown <br />
  15: Unknown <br />
  16: Unknown <br />
  17: Unknown <br />
  18: Unknown <br />
  19:   Unknown <br />
  20: Unknown <br />
  I stopped at 20 <br />
  <br />
  Recovery: <br />
  Shows recovery   in the spell I am unsure of what this does. (time   in hundredths of a second.) 1,0000 = 10 seconds. <br />
  <br />
  Level: <br />
  Minimum   Level needed to cast the spell. <br />
  <br />
  Power_req_Percent: <br />
  The % of power you need remaining in order to   cast the spell. <br />
  <br />
  HP_Req_Percent: <br />
  The %   of HP you need remaining in order to cast the spell. <br />
  <br />
  Success_Message: <br />
  The message you   receive on screen when you successfully cast or receive a spell effect. <br />
  Example: <br />
  You begin to sneak. <br />
  <br />
  Fade_Message: <br />
  The message you receive when the spell is canceled / wears off. <br />
  Example: <br />
  You   stop sneaking. <br />
  <br />
  Lua_Script: <br />
  The name of the lua script in your spells directory. <br />
  Example: <br />
  Sprint <br />
  Damage <br />
  These are case sensitive and do not need the .lua at   the end. <br />
  <br />
  Spell_Visual: <br />
  The visual effect id of the effect you want   to see when the spell is cast. <br />
  <br />
  Effect_Message: <br />
  This is at the bottom of the spell it say something like. increase speed bye 15% <br />
  &quot;for every effect listed you must add 1 number to the number of bullets in   spell_display_effects <br />
  <br />
  Spell_Book_Type: <br />
  What spell category the spell will be scribed in. <br />
  0: Spells <br />
  1: Combat   arts <br />
  2: Trade skills <br />
  <br />
  Interruptible: <br />
  Is the spell able to be interupted.</span> </p>
<p><a name="spelldata"></a></p>
<p class="style3">Table: `spell_data` Field List</p>
<p class="style1">id: auto-increment</p>
<p class="style1">spell_id: reference to spells 'id' value</p>
<p class="style1">tier: what spell tier this dataset belongs to</p>
<p class="style1">index_field: parameter order, starting with 0, incrementing by 1 for every parameter passed to LUA</p>
<p class="style1">value_type: datatype of the parameter</p>
<p class="style1">value: the value of the parameter passed to LUA</p>
<p class="style1">value2: &lt;not used&gt;    </p>
<p class="style3">Table: `spell_data` Usage</p>

<p class="style1">Each 'tier' of a spell requires data to pass into the LUA system (spell script) in order to function. The minimum data required for a damage spell is <strong>Damage Type</strong>, <strong>Damage Amount</strong>.</p>
<p class="style1">The	<strong>standard</strong> script parameters for a Damage spell would be: DmgType, MinDmgVal, MaxDmgVal<br />	
Example: spell(0, 1, 5) would cause between 1 and 5 <strong>slashing</strong> damage on your target </p>
<p class="style1">A DD/DOT script may take many more parameters: DmgTypeDD, MinDmgDD, MaxDmgDD, DmgTypeDOT, MinDmgDot, MaxDmgDot <br />
Example: spell(0, 1, 5, 8, 4, 10) would cause between 1 and 5 <strong>slashing</strong> damage on your target, and cause between 4 and 10 <strong>disease</strong> damage over the set duration of the spell </p>
<p class="style1">These are just examples, but each Tier could require more than 6 spell_data entries, depending on the complexity of your spell. The goal is to utilize as FEW unique scripts as possible, so plan your spell_data with this in mind. <strong><br />
Concept: </strong> LUA will ignore parameters passed that are not used in the code. You can write a DD/DOT script that you can also use just for DD, and check in the script for param value is nil, and skip to just the DD section. </p>
<p class="style1">Damage Type values:
<table width="180" class="style1" border="1" cellspacing="1">
	<tr>
		<th>Damage Type</th>
		<th>Damage Type Value</th>
	</tr>
	<tr>
		<td>Slash</td>
		<td>0</td>
	</tr>
	<tr>
		<td>Crush</td>
		<td>1</td>
	</tr>
	<tr>
		<td>Pierce</td>
		<td>2</td>
	</tr>
	<tr>
		<td>Heat</td>
		<td>3</td>
	</tr>
	<tr>
		<td>Cold</td>
		<td>4</td>
	</tr>
	<tr>
		<td>Magic</td>
		<td>5</td>
	</tr>
	<tr>
		<td>Mental</td>
		<td>6</td>
	</tr>
	<tr>
		<td>Divine</td>
		<td>7</td>
	</tr>
	<tr>
		<td>Disease</td>
		<td>8</td>
	</tr>
	<tr>
		<td>Poison</td>
		<td>9</td>
	</tr>
	<tr>
		<td>Drown</td>
		<td>10</td>
	</tr>
	<tr>
		<td>Falling</td>
		<td>11</td>
	</tr>
	<tr>
		<td>Pain</td>
		<td>12</td>
	</tr>
</table></p>
<p class="style1">An example of a complex spell_data entry for the DD/DOT mentioned above:</p>
<table width="450" class="style1" border="1" cellspacing="1">
	<tr>
		<th>id</th>
		<th>spell_id</th>
		<th>tier</th>
		<th>index_field</th>
		<th>value_type</th>
		<th>value</th>
		<th>value2</th>
	</tr>
	<tr align="center">
		<td>1</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>0</td>
		<td>INT</td>
		<td>0</td>
		<td>0</td>
	</tr>
	<tr align="center">
		<td>2</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>1</td>
		<td>INT</td>
		<td>5</td>
		<td>0</td>
	</tr>
	<tr align="center">
		<td>3</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>2</td>
		<td>INT</td>
		<td>9</td>
		<td>0</td>
	</tr>
	<tr align="center">
		<td>4</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>3</td>
		<td>INT</td>
		<td>8</td>
		<td>0</td>
	</tr>
	<tr align="center">
		<td>5</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>4</td>
		<td>INT</td>
		<td>4</td>
		<td>0</td>
	</tr>
	<tr align="center">
		<td>6</td>
		<td>40000</td>
		<td>Apprentice I</td>
		<td>5</td>
		<td>INT</td>
		<td>10</td>
		<td>0</td>
	</tr>
</table>
<p class="style1">As you can see above, 'id' 1 and 4 are the Damage_Type values, while 'id' 2 and 5 are the MIN damage, and 3 and 6 are the MAX damage. The duration of the DOT component is set in the spell_tiers page under 'duration'. </p>
<p>&nbsp; </p>
<p><a name="scriptfunctions"></a></p>
<p>&nbsp; </p>
<p><a name="items"></a></p>
<p class="style3">Table: `item_stats`</p>
<p class="style1">Here is a list of all known item stat types/subtypes:<br />
<br />
0 => STR,<br />
1 => "STA",<br />
2 => "AGI",<br />
3 => "WIS",<br />
4 => "INT",<br />
200 => "VS_SLASH",<br />
201 => "VS_CRUSH",<br />
202 => "VS_PIERCE",<br />
203 => "VS_HEAT",<br />
204 => "VS_COLD",<br />
205 => "VS_MAGIC",<br />
206 => "VS_MENTAL",<br />
207 => "VS_DIVINE",<br />
208 => "VS_DISEASE",<br />
209 => "VS_POISON",<br />
210 => "VS_DROWNING",<br />
211 => "VS_FALLING",<br />
212 => "VS_PAIN",<br />
213 => "VS_MELEE",<br />
300 => "DMG_SLASH",<br />
301 => "DMG_CRUSH",<br />
302 => "DMG_PIERCE",<br />
303 => "DMG_HEAT",<br />
304 => "DMG_COLD",<br />
305 => "DMG_MAGIC",<br />
306 => "DMG_MENTAL",<br />
307 => "DMG_DIVINE",<br />
308 => "DMG_DISEASE",<br />
309 => "DMG_POISON",<br />
310 => "DMG_DROWNING",<br />
311 => "DMG_FALLING",<br />
312 => "DMG_PAIN",<br />
313 => "DMG_MELEE",<br />
500 => "HEALTH",<br />
501 => "POWER",<br />
502 => "CONCENTRATION",<br />
600 => "HPREGEN",<br />
601 => "MANAREGEN",<br />
602 => "HPREGENPPT",<br />
603 => "MPREGENPPT",<br />
604 => "COMBATHPREGENPPT",<br />
605 => "COMBATMPREGENPPT",<br />
606 => "MAXHP",<br />
607 => "MAXHPPERC",<br />
608 => "SPEED",<br />
609 => "SLOW",<br />
610 => "MOUNTSPEED",<br />
611 => "OFFENSIVESPEED",<br />
612 => "ATTACKSPEED",<br />
613 => "MAXMANA",<br />
614 => "MAXMANAPERC",<br />
615 => "MAXATTPERC",<br />
616 => "BLURVISION",<br />
617 => "MAGICLEVELIMMUNITY",<br />
618 => "HATEGAINMOD",<br />
619 => "COMBATEXPMOD",<br />
620 => "TRADESKILLEXPMOD",<br />
621 => "ACHIEVEMENTEXPMOD",<br />
622 => "SIZEMOD",<br />
623 => "UNKNOWN",<br />
624 => "STEALTH",<br />
625 => "INVIS",<br />
626 => "SEESTEALTH",<br />
627 => "SEEINVIS",<br />
628 => "EFFECTIVELEVELMOD",<br />
629 => "RIPOSTECHANCE",<br />
630 => "PARRYCHANCE",<br />
631 => "DODGECHANCE",<br />
632 => "AEAUTOATTACKCHANCE",<br />
633 => "DOUBLEATTACKCHANCE",<br />
634 => "RANGEDDOUBLEATTACKCHANCE",<br />
635 => "SPELLDOUBLEATTACKCHANCE",<br />
636 => "FLURRY",<br />
637 => "EXTRAHARVESTCHANCE",<br />
638 => "EXTRASHIELDBLOCKCHANCE",<br />
639 => "DEFLECTIONCHANCE",<br />
640 => "ITEMHPREGENPPT",<br />
641 => "ITEMPPREGENPPT",<br />
642 => "MELEECRITCHANCE",<br />
643 => "RANGEDCRITCHANCE",<br />
644 => "DMGSPELLCRITCHANCE",<br />
645 => "HEALSPELLCRITCHANCE",<br />
646 => "MELEECRITBONUS",<br />
647 => "RANGEDCRITBONUS",<br />
648 => "DMGSPELLCRITBONUS",<br />
649 => "HEALSPELLCRITBONUS",<br />
650 => "UNCONSCIOUSHPMOD",<br />
651 => "SPELLTIMEREUSEPCT",<br />
652 => "SPELLTIMERECOVERYPCT",<br />
653 => "SPELLTIMECASTPCT",<br />
654 => "MELEEWEAPONRANGE",<br />
655 => "RANGEDWEAPONRANGE",<br />
656 => "FALLINGDAMAGEREDUCTION",<br />
657 => "SHIELDEFFECTIVENESS",<br />
658 => "RIPOSTEDAMAGE",<br />
659 => "MINIMUMDEFLECTIONCHANCE",<br />
660 => "MOVEMENTWEAVE",<br />
661 => "COMBATHPREGEN",<br />
662 => "COMBATMANAREGEN",<br />
663 => "CONTESTSPEEDBOOST",<br />
664 => "TRACKINGAVOIDANCE",<br />
665 => "STEALTHINVISSPEEDMOD",<br />
666 => "LOOT_COIN",<br />
667 => "ARMORMITIGATIONINCREASE",<br />
668 => "AMMOCONSERVATION",<br />
669 => "STRIKETHROUGH",<br />
670 => "STATUSBONUS",<br />
671 => "ACCURACY",<br />
672 => "COUNTERSTRIKE",<br />
673 => "SHIELDBASH",<br />
674 => "WEAPONDAMAGEBONUS",<br />
675 => "ADDITIONALRIPOSTECHANCE",<br />
676 => "CRITICALMITIGATION",<br />
677 => "COMBATARTDAMAGE",<br />
678 => "SPELLDAMAGE",<br />
679 => "HEALAMOUNT",<br />
680 => "TAUNTAMOUNT",<br />
700 => "SPELL_DAMAGE",<br />
701 => "HEAL_AMOUNT",<br />
702 => "SPELL_AND_HEAL",<br />
</p>

</body>
</html>
