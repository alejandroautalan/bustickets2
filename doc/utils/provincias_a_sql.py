import csv

provincias_csv = "provincias.csv"

with open(provincias_csv, newline='') as csvfile:
    reader = csv.DictReader(csvfile)
    sql = []
    insert_tpl = f"""INSERT INTO provincia (id, nombre) values"""
    sql.append(insert_tpl)
    values = []
    for row in reader:
        insert_values_tpl = f"""({row["id"]}, "{row["nombre"]}")"""
        values.append(insert_values_tpl)
    values = ",\n".join(values)
    sql.append(values)
    sql = "\n".join(sql)
    print(sql, ";")
