from settings import *
import random
import sys

'''
This pattern states that any bar which serves illegal beers must have sex offenders

I do not want to alter sex offender (since this could conflict with the underage pattern) therefore any violation of this pattern will be deleted

Select illegal beers:
SELECT * FROM Beer where Manf in (SELECT m.name FROM Manufacturer m,Country c Where m.country = c.name AND prohibition=1)

'''