SELECT * FROM STIGIT.V_F_PERS per
INNER  JOIN STIGIT.V_F_PODR podr ON podr.KODZIFR = per.KODZIFR
INNER JOIN STIGIT.V_USER_SECURE sec ON per.TN = sec.TN