'''
This pattern states that any bar which serves illegal beers must have sex offenders

I do not want to alter sex offender (since this could conflict with the underage pattern) therefore any violation of this pattern will be deleted

Select illegal beers:
SELECT * FROM Beer where Manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1)


Select violations

SELECT * FROM Sells s 
WHERE s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1))
AND NOT EXISTS ( SELECT * FROM SexOffender WHERE bar = s.bar)

Then delete them from Sells, should be fine. Python file not necessary lol

This is the query which validates it:

SELECT CASE WHEN 
(SELECT COUNT(*) 
FROM Sells s 
WHERE s.beer IN (SELECT name FROM Beer where manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1))
AND NOT EXISTS (SELECT * FROM SexOffender WHERE bar = s.bar)) = 0 THEN 'Yes'
ELSE 'No'
END AS isTrue

'''
